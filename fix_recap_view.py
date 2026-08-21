import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\recap-and-documents.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Fix relation name
content = content.replace('generatedDocuments', 'documents')

# Add links for editing scores
aksi_replacement = """                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex flex-col space-y-2 items-end">
                                <div>
                                    <a href="{{ route('admin.defenses.internship.score', ['caseId' => $case->id, 'role' => 'supervisor']) }}" class="text-blue-600 hover:text-blue-900 text-xs mr-2">Input/Edit Nilai Pembimbing</a>
                                </div>
                                <div>
                                    <a href="{{ route('admin.defenses.internship.score', ['caseId' => $case->id, 'role' => 'examiner']) }}" class="text-blue-600 hover:text-blue-900 text-xs mr-2">Input/Edit Nilai Penguji</a>
                                </div>
                                <div>
                                    <button wire:click="generateDocuments({{ $case->id }})" class="text-indigo-600 hover:text-indigo-900 font-bold">
                                        Generate PDF
                                    </button>
                                </div>"""

import re
content = re.sub(r'<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">\s*<button wire:click="generateDocuments\(\{\{ \$case->id \}\}\)" class="text-indigo-600 hover:text-indigo-900 mr-3">\s*Generate PDF\s*</button>', aksi_replacement, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
