
import docx
from docx.document import Document
from docx.oxml.table import CT_Tbl
from docx.oxml.text.paragraph import CT_P
from docx.table import _Cell, Table
from docx.text.paragraph import Paragraph
import os

def iter_block_items(parent):
    if isinstance(parent, Document):
        parent_elm = parent.element.body
    elif isinstance(parent, _Cell):
        parent_elm = parent._tc
    else:
        raise TypeError("Unknown parent type")

    for child in parent_elm.iterchildren():
        if isinstance(child, CT_P):
            yield Paragraph(child, parent)
        elif isinstance(child, CT_Tbl):
            yield Table(child, parent)

def extract_images_in_doc_order(file_path, output_dir):
    doc = docx.Document(file_path)
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
    
    image_count = 0
    for block in iter_block_items(doc):
        if isinstance(block, Paragraph):
            # Find all images in this paragraph
            for run in block.runs:
                if 'graphic' in run.element.xml:
                    # Extract the image
                    # This is a bit tricky with python-docx
                    # We can use the blip rId to find the image in the document part
                    # For simplicity, let's find all rIds in the XML
                    import re
                    rIds = re.findall(r'r:embed="([^"]+)"', run.element.xml)
                    for rId in rIds:
                        image_count += 1
                        image_part = doc.part.related_parts[rId]
                        img_data = image_part.blob
                        ext = image_part.content_type.split('/')[-1]
                        img_name = f"doc_order_image_{image_count}.{ext}"
                        with open(os.path.join(output_dir, img_name), "wb") as f:
                            f.write(img_data)
                        print(f"Extracted {img_name}")

if __name__ == "__main__":
    extract_images_in_doc_order("CAUSE DASHBORDS.docx", "scratch/dashboard_images_ordered")
