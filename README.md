# I Ching Consultation Oracle

A lightweight, web-based I Ching (Book of Changes) consultation tool built with a traditional LAMP stack (PHP & MySQL). It simulates a single-hexagram reading based on a focused user inquiry and renders matching visual line diagrams generated via Python.

## Features

- **Query-Driven Interface:** Requires the user to enter a focused question before casting a reading.
- **Random Hexagram Selection:** Pulls hexagram text, translations, and interpretations dynamically from a MySQL database.
- **Visual Representations:** Includes automatically generated line diagrams (64 JPEG images) displaying solid (Yang) and broken (Yin) lines.
- **External Deep-Links:** Generates direct contextual links to both Wikipedia and Divination.com for deeper study of the cast hexagram.
- **Responsive Layout:** Clean, minimalist styling built with Times Roman typography and Water.css.

## Project Structure
i-ching/
├── index.php                 # Core application logic and presentation
├── generate_hexagrams.py     # Python script to generate the 64 JPEG images
├── images/                   # Generated hexagram JPEGs (hexagram-1.jpg to hexagram-64.jpg)
└── README.md                 # Project documentation
## Setup & Local Development

### Prerequisites

- **LAMP Stack:** Apache, PHP 8.x, MySQL / MariaDB
- **Python 3:** With `Pillow` (PIL) library installed for image generation

### Database Setup

1. Create a database (e.g., `iching_db`).
2. Create the `iching_hexagrams` table and populate it with hexagram records (`hexagram_number`, `chinese_pinyin`, `english_translation`, `core_meaning`, `description`).

### Image Generation

To generate or rebuild the 64 hexagram JPEGs, run the Python generator script from the project root:

bash:
python3 generate_hexagrams.py

This populates the images/ directory with hexagram-1.jpg through hexagram-64.jpg.
