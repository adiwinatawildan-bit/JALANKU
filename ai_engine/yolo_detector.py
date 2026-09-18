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
import tempfile
from typing import Any, Dict, List

# Force strictly offline mode and disable GPU probing to prevent network hangs & driver timeouts
os.environ["YOLO_OFFLINE"] = "True"
os.environ["ULTRALYTICS_OFFLINE"] = "True"
os.environ["YOLO_AUTOINSTALL"] = "False"
os.environ["ULTRALYTICS_AUTOINSTALL"] = "False"
os.environ["CUDA_VISIBLE_DEVICES"] = ""
os.environ["YOLO_VERBOSE"] = "False"

# Ensure writable temp directories for container environments (Render, Docker, www-data)
_safe_tmp = tempfile.gettempdir()
os.environ.setdefault("YOLO_CONFIG_DIR", _safe_tmp)
os.environ.setdefault("TORCH_HOME", _safe_tmp)

# Prevent Ultralytics from attempting to download Arial.ttf or checking PyPI in container
try:
    import ultralytics.utils.checks as _checks
    _checks.check_font = lambda font="Arial.ttf": ""
    _checks.check_latest_pypi_version = lambda *args, **kwargs: None
    _checks.check_version = lambda *args, **kwargs: True
    _checks.check_requirements = lambda *args, **kwargs: True
except Exception:
    pass


def analyze_image_onnx(target_image_path: str, onnx_path: str, conf_threshold: float = 0.05) -> Dict[str, Any] | None:
    """Ultra-fast, low-memory inference using OpenCV DNN and Kaggle ONNX weights (~20MB RAM, <0.3s)."""
    try:
        import cv2
        import numpy as np

        img = cv2.imread(target_image_path)
        if img is None:
            return None
        orig_h, orig_w = img.shape[:2]

        scale = min(800.0 / orig_w, 800.0 / orig_h)
        nw, nh = int(round(orig_w * scale)), int(round(orig_h * scale))
        resized = cv2.resize(img, (nw, nh), interpolation=cv2.INTER_LINEAR)
        padded = np.full((800, 800, 3), 114, dtype=np.uint8)
        dx = (800 - nw) // 2
        dy = (800 - nh) // 2
        padded[dy:dy + nh, dx:dx + nw] = resized

        blob = cv2.dnn.blobFromImage(padded, scalefactor=1.0 / 255.0, size=(800, 800), swapRB=True, crop=False)
        net = cv2.dnn.readNetFromONNX(onnx_path)
        net.setInput(blob)
        output = net.forward()[0]  # shape: (7, 13125)

        predictions = output.T
        boxes = []
        confidences = []
        class_ids = []

        for pred in predictions:
            scores = pred[4:]
            cls_id = int(np.argmax(scores))
            conf = float(scores[cls_id])
            if conf >= conf_threshold:
                cx, cy, w, h = pred[0], pred[1], pred[2], pred[3]
                x1 = int(round((cx - w / 2.0 - dx) / scale))
                y1 = int(round((cy - h / 2.0 - dy) / scale))
                x2 = int(round((cx + w / 2.0 - dx) / scale))
                y2 = int(round((cy + h / 2.0 - dy) / scale))
                x1 = max(0, min(orig_w, x1))
                y1 = max(0, min(orig_h, y1))
                x2 = max(0, min(orig_w, x2))
                y2 = max(0, min(orig_h, y2))
                bw = x2 - x1
                bh = y2 - y1
                if bw > 2 and bh > 2:
                    boxes.append([x1, y1, bw, bh])
                    confidences.append(conf)
                    class_ids.append(cls_id)

        indices = cv2.dnn.NMSBoxes(boxes, confidences, score_threshold=conf_threshold, nms_threshold=0.5)

        class_map = {0: "crack", 1: "landslide", 2: "pothole"}
        potholes = 0
        cracks = 0
        landslides = 0
        boxes_data = []

        if len(indices) > 0:
            for idx in np.array(indices).flatten():
                c_id = class_ids[idx]
                cls_key = class_map.get(c_id, "normal")
                conf_val = round(confidences[idx] * 100.0, 1)
                bx = boxes[idx]
                xyxy = [bx[0], bx[1], bx[0] + bx[2], bx[1] + bx[3]]

                if cls_key == "landslide":
                    landslides += 1
                elif cls_key == "pothole":
                    potholes += 1
                elif cls_key == "crack":
                    cracks += 1

                boxes_data.append({
                    "class": cls_key,
                    "confidence": conf_val,
                    "box": xyxy,
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

            return {
                "success": True,
                "total_defects": total,
                "confidence_score": round(max(b["confidence"] for b in boxes_data), 1),
                "detected_classes": {
                    "pothole": potholes,
                    "crack": cracks,
                    "landslide": landslides,
                },
                "damaged_area_sqm": area_sqm,
                "bounding_boxes": boxes_data,
                "model_version": "YOLO-Kaggle-Custom-v2.0 (model_terbaru_kaggle.onnx - OpenCV DNN)",
            }
        else:
            return {
                "success": True,
                "total_defects": 0,
                "confidence_score": 0.0,
                "detected_classes": {
                    "pothole": 0,
                    "crack": 0,
                    "landslide": 0,
                },
                "damaged_area_sqm": 0.0,
                "bounding_boxes": [],
                "model_version": "YOLO-Kaggle-Custom-v2.0 (model_terbaru_kaggle.onnx - OpenCV DNN)",
                "status": "normal",
            }
    except Exception as e:
        sys.stderr.write(f"ONNX DNN inference notice: {str(e)}\n")
        return None


def analyze_image(image_path: str, confidence_threshold: float = 0.05, imgsz: int = 384) -> Dict[str, Any]:
    """Analyze road damage using Kaggle trained model with OpenCV DNN (primary) or PyTorch (fallback)."""
    import urllib.request

    is_url = image_path.startswith("http://") or image_path.startswith("https://")
    temp_download_path = None
    temp_resized_path = None

    try:
        if is_url:
            try:
                temp_file = tempfile.NamedTemporaryFile(delete=False, suffix=".jpg")
                temp_download_path = temp_file.name
                temp_file.close()

                req = urllib.request.Request(
                    image_path,
                    headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
                )
                with urllib.request.urlopen(req, timeout=25) as response, open(temp_download_path, "wb") as out_file:
                    out_file.write(response.read())
                target_image_path = temp_download_path
            except Exception as e:
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

        # Check for Kaggle ONNX weights (ultra-fast, ~20MB RAM, finishes in <0.3s)
        base_dir = os.path.dirname(os.path.abspath(__file__))
        onnx_path = os.path.join(base_dir, "weights", "model_terbaru_kaggle.onnx")
        if not os.path.exists(onnx_path):
            onnx_path = os.path.join(os.getcwd(), "ai_engine", "weights", "model_terbaru_kaggle.onnx")

        if os.path.exists(onnx_path):
            onnx_res = analyze_image_onnx(target_image_path, onnx_path, confidence_threshold)
            if onnx_res is not None:
                return onnx_res

        # FALLBACK ENGINE: PyTorch & Ultralytics
        # Downscale large image or convert PNG/RGBA to lightweight JPEG to protect memory
        try:
            from PIL import Image
            with Image.open(target_image_path) as img:
                w, h = img.size
                if max(w, h) > 800 or img.format != "JPEG" or img.mode != "RGB":
                    scale = min(800.0 / max(w, h), 1.0)
                    new_size = (max(1, int(w * scale)), max(1, int(h * scale)))
                    resample_mode = Image.Resampling.LANCZOS if hasattr(Image, "Resampling") else Image.LANCZOS
                    resized_img = img.convert("RGB").resize(new_size, resample=resample_mode)

                    temp_resized = tempfile.NamedTemporaryFile(delete=False, suffix=".jpg")
                    temp_resized_path = temp_resized.name
                    temp_resized.close()
                    resized_img.save(temp_resized_path, "JPEG", quality=85)
                    target_image_path = temp_resized_path
        except Exception:
            pass

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
        
        # Predict with custom trained weights (CPU, max 10 detections, imgsz)
        detections = model.predict(target_image_path, conf=confidence_threshold, verbose=False, imgsz=imgsz, max_det=10, device='cpu')

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
            results["confidence_score"] = 0.0
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
        if temp_resized_path and os.path.exists(temp_resized_path):
            try:
                os.remove(temp_resized_path)
            except Exception:
                pass

    return results


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="YOLO Road Damage Detector")
    parser.add_argument("--image", type=str, required=True, help="Path to image file or URL")
    parser.add_argument("--conf", type=float, default=0.05, help="Confidence threshold")
    parser.add_argument("--imgsz", type=int, default=384, help="Inference image size")

    args = parser.parse_args()
    output = analyze_image(args.image, args.conf, args.imgsz)
    print(json.dumps(output, indent=2))
