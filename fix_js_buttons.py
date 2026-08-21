import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\js\mentor-pdf-viewer.js'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Find the block where we attach event listeners
old_block = """    document.getElementById('pdf-prev')?.addEventListener('click', onPrevPage);
    document.getElementById('pdf-next')?.addEventListener('click', onNextPage);
    document.getElementById('pdf-zoom-in')?.addEventListener('click', onZoomIn);
    document.getElementById('pdf-zoom-out')?.addEventListener('click', onZoomOut);
    document.getElementById('pdf-fit')?.addEventListener('click', onFitWidth);"""

new_block = """    // Use event delegation to handle Livewire DOM re-renders
    document.addEventListener('click', (e) => {
        if (e.target.closest('#pdf-prev')) { e.preventDefault(); onPrevPage(); }
        else if (e.target.closest('#pdf-next')) { e.preventDefault(); onNextPage(); }
        else if (e.target.closest('#pdf-zoom-in')) { e.preventDefault(); onZoomIn(); }
        else if (e.target.closest('#pdf-zoom-out')) { e.preventDefault(); onZoomOut(); }
        else if (e.target.closest('#pdf-fit')) { e.preventDefault(); onFitWidth(); }
        else if (e.target.closest('#pdf-retry-btn')) { 
            e.preventDefault(); 
            if (currentPreviewUrl) loadPdf(currentPreviewUrl); 
        }
    });"""

content = content.replace(old_block, new_block)

# Also remove the old retry listener
old_retry = """    document.getElementById('pdf-retry-btn')?.addEventListener('click', () => {
        if (currentPreviewUrl) {
            loadPdf(currentPreviewUrl);
        }
    });"""
content = content.replace(old_retry, "")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
