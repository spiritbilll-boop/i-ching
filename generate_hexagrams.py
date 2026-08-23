import os
from PIL import Image, ImageDraw

# Create images directory if it doesn't exist
os.makedirs("images", exist_ok=True)

# King Wen Sequence: Hexagram 1-64 binary strings (Line 1 to Line 6, bottom-to-top)
# 1 = Yang (Solid line ???), 0 = Yin (Broken line ? ?)
KING_WEN_BINARY = {
    1: "111111",  2: "000000",  3: "100010",  4: "010001",  5: "111010",
    6: "010111",  7: "010000",  8: "000010",  9: "111011", 10: "110111",
   11: "111000", 12: "000111", 13: "101111", 14: "111101", 15: "001000",
   16: "000100", 17: "100110", 18: "011001", 19: "110000", 20: "000011",
   21: "100101", 22: "101001", 23: "000001", 24: "100000", 25: "100111",
   26: "111001", 27: "100001", 28: "011110", 29: "010010", 30: "101101",
   31: "001110", 32: "011100", 33: "001111", 34: "111100", 35: "000101",
   36: "101000", 37: "101011", 38: "110101", 39: "001010", 40: "010100",
   41: "110001", 42: "100011", 43: "111110", 44: "011111", 45: "000110",
   46: "011000", 47: "010110", 48: "011010", 49: "101110", 50: "011101",
   51: "100100", 52: "001001", 53: "001011", 54: "110100", 55: "101100",
   56: "001101", 57: "011011", 58: "110110", 59: "010011", 60: "110010",
   61: "110011", 62: "001100", 63: "101010", 64: "010101"
}

def create_hexagram_image(hex_num, binary_str):
    # Image Canvas Dimensions
    width, height = 300, 360
    bg_color = (253, 250, 246) # Warm parchment off-white
    line_color = (20, 20, 20)   # Dark Charcoal / Off-black
    
    img = Image.new("RGB", (width, height), color=bg_color)
    draw = ImageDraw.Draw(img)

    # Line formatting dimensions
    margin_x = 40
    line_width = width - (margin_x * 2) # 220px total width for a line
    line_thickness = 18
    gap_between_lines = 24
    yin_gap = 36 # Middle gap width for broken line

    # Convert binary_str into top-to-bottom order for drawing (Line 6 down to Line 1)
    # The King Wen map stores index 0 as Line 1 (bottom), so we reverse string for rendering
    lines_to_draw = binary_str[::-1]

    start_y = 40

    for i, line_type in enumerate(lines_to_draw):
        y = start_y + i * (line_thickness + gap_between_lines)

        if line_type == "1":
            # Draw Solid Yang Line ???????
            draw.rectangle(
                [margin_x, y, margin_x + line_width, y + line_thickness],
                fill=line_color
            )
        else:
            # Draw Broken Yin Line ???  ???
            half_segment_width = (line_width - yin_gap) // 2
            
            # Left Segment
            draw.rectangle(
                [margin_x, y, margin_x + half_segment_width, y + line_thickness],
                fill=line_color
            )
            # Right Segment
            draw.rectangle(
                [margin_x + half_segment_width + yin_gap, y, margin_x + line_width, y + line_thickness],
                fill=line_color
            )

    # Save output JPEG
    output_path = os.path.join("images", f"hexagram-{hex_num}.jpg")
    img.save(output_path, "JPEG", quality=95)

# Generate all 64 JPEGs
print("Generating 64 Hexagram JPEGs...")
for num, binary_pattern in KING_WEN_BINARY.items():
    create_hexagram_image(num, binary_pattern)

print("Done! All 64 images are saved in the 'images/' folder.")
