
import os
import docx
from docx.shared import Inches

def inspect_docx(file_path, output_dir):
    doc = docx.Document(file_path)
    
    # Extract text to understand formatting
    print("--- DOCUMENT TEXT ---")
    for i, para in enumerate(doc.paragraphs):
        if para.text.strip():
            print(f"P{i}: {para.text}")
    
    # Extract images
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
    
    print("\n--- EXTRACTING IMAGES ---")
    image_count = 0
    for rel in doc.part.rels.values():
        if "image" in rel.target_ref:
            image_count += 1
            img_data = rel.target_part.blob
            img_name = f"image_{image_count}.png"
            img_path = os.path.join(output_dir, img_name)
            with open(img_path, "wb") as f:
                f.write(img_data)
            print(f"Saved {img_name}")

if __name__ == "__main__":
    file_path = r"CAUSE DASHBORDS.docx"
    output_dir = r"scratch/dashboard_images"
    inspect_docx(file_path, output_dir)
