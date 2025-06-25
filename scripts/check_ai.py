import sys
import os
import json
import cv2
import torch
from PIL import Image
from transformers import AutoImageProcessor, AutoModelForImageClassification

# Load model and processor
model_name = "Organika/sdxl-detector"
processor = AutoImageProcessor.from_pretrained(model_name)
model = AutoModelForImageClassification.from_pretrained(model_name)

def classify_image(image):
    image = image.convert("RGB")
    inputs = processor(images=image, return_tensors="pt")
    with torch.no_grad():
        outputs = model(**inputs)
    score = torch.nn.functional.softmax(outputs.logits, dim=-1)[0][1].item()
    return score

def analyze_photo(path):
    image = Image.open(path)
    score = classify_image(image)
    flagged = score > 0.7
    return {
        "flagged": flagged,
        "reason": "High AI likelihood" if flagged else "Low AI likelihood",
        "scores": [score]
    }

def analyze_video(path, num_frames=5):
    cap = cv2.VideoCapture(path)
    if not cap.isOpened():
        return {"error": "Cannot open video file"}

    frame_count = int(cap.get(cv2.CAP_PROP_FRAME_COUNT))
    if frame_count == 0:
        return {"error": "Video contains no frames"}

    interval = max(frame_count // num_frames, 1)
    flagged_frames = 0
    scores = []

    for i in range(num_frames):
        frame_no = min(i * interval, frame_count - 1)
        cap.set(cv2.CAP_PROP_POS_FRAMES, frame_no)
        ret, frame = cap.read()
        if not ret:
            continue

        image = Image.fromarray(cv2.cvtColor(frame, cv2.COLOR_BGR2RGB))
        score = classify_image(image)
        scores.append(score)

        if score > 0.7:
            flagged_frames += 1

    cap.release()
    flagged = flagged_frames >= 2

    return {
        "flagged": flagged,
        "reason": f"{flagged_frames} of {num_frames} frames flagged" if flagged else "Low AI likelihood",
        "scores": scores
    }

def check_media(path):
    ext = os.path.splitext(path)[1].lower()
    if ext in [".mp4", ".mov", ".avi"]:
        return analyze_video(path)
    elif ext in [".jpg", ".jpeg", ".png"]:
        return analyze_photo(path)
    else:
        return {"error": "Unsupported file type"}

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({"error": "Usage: python3 check_ai.py <file_path>"}))
    else:
        result = check_media(sys.argv[1])
        print(json.dumps(result))
