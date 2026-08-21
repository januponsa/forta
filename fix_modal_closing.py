import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\form-manager.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

pattern = r'</svg>\s*</button>\s*</div>\s*<div class="p-6 overflow-y-auto">'
replacement = """</svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 overflow-y-auto">"""

content = re.sub(pattern, replacement, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
