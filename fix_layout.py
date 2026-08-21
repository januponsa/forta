import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Lecturer\Defense\MyDefenses.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("        ]);\n    }\n}", "        ])->layout('layouts.admin');\n    }\n}")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
