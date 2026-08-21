import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\Dashboard.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('use Livewire\Component;', 'use Livewire\Component;\nuse Livewire\WithPagination;')
content = content.replace('class Dashboard extends Component\n{', 'class Dashboard extends Component\n{\n    use WithPagination;\n')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
