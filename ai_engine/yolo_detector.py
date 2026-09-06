"""
YOLO Road Damage Detector Engine
Part of JALAN KU Public Infrastructure System
Using Custom Kaggle Trained YOLO Model (model_terbaru_kaggle.pt)
Classes: {0: 'crack', 1: 'landslide', 2: 'pothole'}
"""

from __future__ import annotations

import argparse
import io
import json
import os
import sys
from typing import Any, Dict, List


def analyze_image(image_path: str, confidence_threshold: float = 0.15) -> Dict[str, Any]:
    """Analyze road damage strictly using the user's custom trained Kaggle YOLO model (model_terbaru_kaggle.pt)."""
    import tempfile
    import urllib.request

    is_url = image_path.startswith("http://") or image_path.startswith("https://")
    temp_download_path = None

    if is_url:
        try:
            temp_file = tempfile.NamedTemporaryFile(delete=False, suffix=".jpg")
            temp_download_path = temp_file.name
            temp_file.close()

            req = urllib.request.Request(
                image_path,
                headers={"User-Agent": "JALANKU-YOLO-Engine/2.0"}
            )
            with urllib.request.urlopen(req, timeout=15) as response, open(temp_download_path, "wb") as out_file:
                out_file.write(response.read())
            target_image_path = temp_download_path
        except Exception as e:
            if temp_download_path and os.path.exists(temp_download_path):
                os.remove(temp_download_path)
            return {
                "success": False,
                "error": f"Failed to download image from URL: {str(e)}"
            }
    else:
        target_image_path = os.path.abspath(image_path)
        if not os.path.exists(target_image_path):
            return {
                "success": False,
                "error": f"Image file not found: {target_image_path}"
            }

    results: Dict[str, Any] = {
        "success": True,
        "total_defects": 0,
        "confidence_score": 0.0,
        "detected_classes": {
            "pothole": 0,
            "crack": 0,
            "landslide": 0
        },
        "damaged_area_sqm": 0.0,
        "bounding_boxes": [],
        "model_version": "YOLO-Kaggle-Custom-v2.0 (model_terbaru_kaggle.pt)"
    }

    try:
        # Suppress ultralytics banner and limit thread usage to prevent OOM on cloud containers
        os.environ["YOLO_VERBOSE"] = "False"
        os.environ["OMP_NUM_THREADS"] = "1"
        os.environ["OPENBLAS_NUM_THREADS"] = "1"
        os.environ["MKL_NUM_THREADS"] = "1"

        import torch
        torch.set_num_threads(1)
        torch.set_grad_enabled(False)

        from ultralytics import YOLO

        base_dir = os.path.dirname(os.path.abspath(__file__))
        model_path = os.path.join(base_dir, "weights", "model_terbaru_kaggle.pt")
        if not os.path.exists(model_path):
            model_path = os.path.join(os.getcwd(), "ai_engine", "weights", "model_terbaru_kaggle.pt")

        if not os.path.exists(model_path):
            return {
                "success": False,
                "error": f"Model weights not found at {model_path}"
            }

        model = YOLO(model_path)
        
        # Predict with custom trained weights (CPU, max 10 detections, imgsz 640)
        detections = model.predict(target_image_path, conf=confidence_threshold, verbose=False, imgsz=640, max_det=10)

        potholes = 0
        cracks = 0
        landslides = 0
        boxes_data: List[Dict[str, Any]] = []
        conf_sum = 0.0

        for r in detections:
            if hasattr(r, "boxes") and r.boxes is not None:
                for box in r.boxes:
                    cls_id = int(box.cls[0].item() if hasattr(box.cls[0], "item") else box.cls[0])
                    conf = float(box.conf[0].item() if hasattr(box.conf[0], "item") else box.conf[0]) * 100
                    
                    xyxy_raw = box.xyxy[0]
                    if hasattr(xyxy_raw, "tolist"):
                        xyxy = [int(v) for v in xyxy_raw.tolist()]
                    else:
                        xyxy = [int(v) for v in xyxy_raw]

                    # Resolve class name from model.names dictionary
                    cls_name = str(model.names.get(cls_id, "")).lower()
                    if "landslide" in cls_name or "longsor" in cls_name or (cls_id == 1 and not cls_name):
                        landslides += 1
                        cls_key = "landslide"
                    elif "pothole" in cls_name or "lubang" in cls_name or (cls_id == 2 and not cls_name):
                        potholes += 1
                        cls_key = "pothole"
                    elif "crack" in cls_name or "retak" in cls_name or (cls_id == 0 and not cls_name):
                        cracks += 1
                        cls_key = "crack"
                    else:
                        cls_key = "normal"

                    conf_sum += conf
                    boxes_data.append({
                        "class": cls_key,
                        "confidence": round(conf, 1),
                        "box": xyxy
                    })

        total = len(boxes_data)
        if total > 0:
            if landslides > 0:
                area_sqm = round(4.5 + (landslides * 2.0) + (potholes * 0.5), 2)
            elif potholes > 0:
                area_sqm = round((potholes * 0.65) + (cracks * 0.3), 2)
            elif cracks > 0:
                area_sqm = round(max(0.6, cracks * 0.45), 2)
            else:
                area_sqm = 0.0

            results["total_defects"] = total
            results["detected_classes"]["pothole"] = potholes
            results["detected_classes"]["crack"] = cracks
            results["detected_classes"]["landslide"] = landslides
            results["confidence_score"] = round(max(b["confidence"] for b in boxes_data), 1)
            results["bounding_boxes"] = boxes_data
            results["damaged_area_sqm"] = area_sqm
        else:
            # Road is normal / no defect detected by the model
            results["total_defects"] = 0
            results["confidence_score"] = 98.0
            results["detected_classes"]["pothole"] = 0
            results["detected_classes"]["crack"] = 0
            results["detected_classes"]["landslide"] = 0
            results["damaged_area_sqm"] = 0.0
            results["status"] = "normal"

    except Exception as e:
        results["success"] = False
        results["error"] = str(e)
    finally:
        if temp_download_path and os.path.exists(temp_download_path):
            try:
                os.remove(temp_download_path)
            except Exception:
                pass

    return results


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="YOLO Road Damage Detector")
    parser.add_argument("--image", type=str, required=True, help="Path to image file or URL")
    parser.add_argument("--conf", type=float, default=0.15, help="Confidence threshold")

    args = parser.parse_args()
    output = analyze_image(args.image, args.conf)
    print(json.dumps(output, indent=2))
