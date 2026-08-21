import sys

file_path = r'c:\Users\userJ\Documents\fortain\vite.config.js'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("'resources/js/signature-editor.js'", "'resources/js/signature-editor.js', 'resources/js/mentor-pdf-viewer.js'")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
