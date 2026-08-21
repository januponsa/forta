import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\js\mentor-pdf-viewer.js'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """    const loadPdf = (url) => {
        if (!url) return;
        currentPreviewUrl = url;
        
        canvas = document.getElementById('pdf-render-canvas');
        if (canvas) {
            // Always get new context since canvas might be recreated by Livewire
            ctx = canvas.getContext('2d');
        }"""
        
# Replace the block that I just inserted
old_block = """    const loadPdf = (url) => {
        if (!url) return;
        currentPreviewUrl = url;
        
        canvas = document.getElementById('pdf-render-canvas');
        if (canvas && !ctx) {
            ctx = canvas.getContext('2d');
        }"""

content = content.replace(old_block, replacement)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
