import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\student\defense\final-result.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace('generatedDocuments', 'documents')

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
