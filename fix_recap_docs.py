import sys

file_path = r'c:\Users\userJ\Documents\fortain\app\Livewire\Admin\Defense\RecapAndDocuments.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("with('student', 'generatedDocuments')", "with('student', 'documents')")

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
