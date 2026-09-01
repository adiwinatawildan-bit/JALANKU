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


def analyze_image(image_path: str, confidence_threshold: float = 0.05) -> Dict[str, Any]:
    """Analyze road damage strictly using the user's custom trained Kaggle YOLO model (model_terbaru_kaggle.pt)."""
    abs_image_path = os.path.abspath(image_path)
    if not os.path.exists(abs_image_path):
        return {
            "success": False,
            "error": f"Image file not found: {abs_image_path}"
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
        # Suppress ultralytics banner during import and predict
        os.environ["YOLO_VERBOSE"] = "False"
        from ultralytics import YOLO

        base_dir = os.path.dirname(os.path.abspath(__file__))
        model_path = os.path.join(base_dir, "weights", "model_terbaru_kaggle.pt")
        if not os.path.exists(model_path):
            model_path = os.path.join(os.getcwd(), "ai_engine", "weights", "model_terbaru_kaggle.pt")

        if os.path.exists(model_path):
            model = YOLO(model_path)
            
            # Predict with trained weights - verbose=False
            detections = model.predict(abs_image_path, conf=confidence_threshold, verbose=False)

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

                        # Direct class mapping according to model_terbaru_kaggle.pt:
                        # 0: crack, 1: landslide, 2: pothole
                        if cls_id == 1:
                            landslides += 1
                            cls_key = "landslide"
                        elif cls_id == 0:
                            cracks += 1
                            cls_key = "crack"
                        else:
                            potholes += 1
                            cls_key = "pothole"

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
                else:
                    area_sqm = round(max(0.6, cracks * 0.45), 2)

                results["total_defects"] = total
                results["detected_classes"]["pothole"] = potholes
                results["detected_classes"]["crack"] = cracks
                results["detected_classes"]["landslide"] = landslides
                results["confidence_score"] = round(conf_sum / max(1, total), 2)
                results["bounding_boxes"] = boxes_data
                results["damaged_area_sqm"] = area_sqm
                return results

    except Exception as e:
        results["success"] = False
        results["error"] = str(e)

    return results


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="YOLO Road Damage Detector")
    parser.add_argument("--image", type=str, required=True, help="Path to image file")
    parser.add_argument("--conf", type=float, default=0.05, help="Confidence threshold")

    args = parser.parse_args()
    output = analyze_image(args.image, args.conf)
    print(json.dumps(output, indent=2))
