import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\form-manager.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

pattern = r'<td class="px-3 py-2 text-sm text-center">\s*@if\(\$sem->is_active\)\s*<span class="px-2 inline-flex text-\[10px\] leading-4 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>\s*@else\s*<span class="px-2 inline-flex text-\[10px\] leading-4 font-semibold rounded-full bg-gray-100 text-gray-800">Nonaktif</span>\s*@endif\s*</td>'

replacement = """<td class="px-3 py-2 text-sm text-center">
                                                  @if($sem->is_active)
                                                      <button wire:click="toggleSemesterStatus({{ $sem->id }})" class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer transition-colors" title="Klik untuk menonaktifkan">Aktif</button>
                                                  @else
                                                      <button wire:click="toggleSemesterStatus({{ $sem->id }})" class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 cursor-pointer transition-colors" title="Klik untuk mengaktifkan">Nonaktif</button>
                                                  @endif
                                              </td>"""

content = re.sub(pattern, replacement, content, flags=re.DOTALL)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
