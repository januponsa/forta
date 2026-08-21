import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\user-manager.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix wire:key in the loop
content = content.replace(
    '<tr class="hover:bg-slate-50 transition-colors">',
    '<tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-50 transition-colors">'
)

# Fix modal wire:ignore.self
content = content.replace(
    '<div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">',
    '<div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:ignore.self>'
)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
