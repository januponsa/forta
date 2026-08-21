import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\mentor-score-input.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """                            @if($documentUrl)
                                <object data="{{ $documentUrl }}" type="application/pdf" class="flex-1 w-full border border-gray-300 rounded" width="100%" height="100%">
                                    <embed src="{{ $documentUrl }}" type="application/pdf" width="100%" height="100%" />
                                </object>
                            @else"""

import re
pattern = re.compile(r'\s*@if\(\$documentUrl\).*?<iframe src="\{\{ \$documentUrl \}\}" class="flex-1 w-full border border-gray-300 rounded" title="Preview PDF"></iframe>.*?@else', re.DOTALL)
content = pattern.sub('\n' + replacement, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
