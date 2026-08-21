import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\recap-and-documents.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix overflow-hidden
content = content.replace('<div class="bg-white shadow overflow-hidden sm:rounded-md">', '<div class="bg-white shadow overflow-visible sm:rounded-md">')

# Fix links
old_loop = """                                                @foreach($case->documents as $doc)
                                                    <a href="/storage/{{ $doc->file_path }}" target="_blank" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">{{ $doc->file_name }}</a>
                                                @endforeach"""

new_loop = """                                                @foreach($case->documents as $doc)
                                                    <a href="{{ route('admin.defenses.internship.documents.download', $doc->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">{{ $doc->original_name ?? 'Dokumen' }}</a>
                                                @endforeach"""

content = content.replace(old_loop, new_loop)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
