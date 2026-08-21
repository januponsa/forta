import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\mentor-score-input.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """</div>

@push('scripts')
    @vite('resources/js/mentor-pdf-viewer.js')
@endpush
"""

content = content.replace("</div>\n", replacement)

# Because there are multiple "</div>", I should only replace the last one. Or I can just append it.
content = content.rstrip()
if not content.endswith("@endpush"):
    content = content + "\n\n@push('scripts')\n    @vite('resources/js/mentor-pdf-viewer.js')\n@endpush\n"

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
