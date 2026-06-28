
import docx
from docx.document import Document
from docx.oxml.table import CT_Tbl
from docx.oxml.text.paragraph import CT_P
from docx.table import _Cell, Table
from docx.text.paragraph import Paragraph

def get_images_in_order(doc):
    images = []
    for rel in doc.part.rels.values():
        if "image" in rel.target_ref:
            images.append(rel.target_part.blob)
    return images

def inspect_structure(file_path):
    doc = docx.Document(file_path)
    
    print("--- Document Content Order ---")
    image_index = 1
    for block in iter_block_items(doc):
        if isinstance(block, Paragraph):
            print(f"[Para] {block.text}")
            # Check if paragraph contains an image
            if 'graphic' in block._p.xml:
                print(f"  -> Contains IMAGE {image_index}")
                image_index += 1
        elif isinstance(block, Table):
            print(f"[Table] with {len(block.rows)} rows")

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

if __name__ == "__main__":
    inspect_structure("CAUSE DASHBORDS.docx")
