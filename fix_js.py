import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\js\mentor-pdf-viewer.js'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the outer declarations
content = content.replace("canvas = document.getElementById('pdf-render-canvas'),", "canvas = null,")

# In loadPdf, we should grab the canvas
replacement = """    const loadPdf = (url) => {
        if (!url) return;
        currentPreviewUrl = url;
        
        canvas = document.getElementById('pdf-render-canvas');
        if (canvas && !ctx) {
            ctx = canvas.getContext('2d');
        }"""
        
content = content.replace("""    const loadPdf = (url) => {
        if (!url) return;
        currentPreviewUrl = url;""", replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
