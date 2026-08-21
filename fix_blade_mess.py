import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\mentor-score-input.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Remove all occurrences of the mistakenly inserted block
pattern = re.compile(r'\n*@push\(\'scripts\'\)\s*@vite\(\'resources/js/mentor-pdf-viewer\.js\'\)\s*@endpush', re.DOTALL)
content = pattern.sub('', content)

# Add it just once at the very end
content = content.strip() + "\n\n@push('scripts')\n    @vite('resources/js/mentor-pdf-viewer.js')\n@endpush\n"

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
