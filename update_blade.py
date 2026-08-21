import sys

with open(r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\calendar-manager.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add buttons below the Semester list
old_semester_btns = '''                            <div class="absolute right-4 top-4 flex space-x-2">
                                <button wire:click="editSemester({{ $sem->id }})" class="text-xs text-gray-500 hover:text-blue-600">
                                    {{ $sem->source_document_code ? 'Lihat' : 'Edit' }}
                                </button>
                                @if(!$sem->source_document_code)
                                    <button wire:click="deleteSemester({{ $sem->id }})" onclick="confirm('Hapus semester ini beserta semua agendanya?') || event.stopImmediatePropagation()" class="text-xs text-gray-500 hover:text-red-600">Del</button>
                                @endif
                            </div>'''

new_semester_btns = '''                            <div class="absolute right-4 top-4 flex space-x-2">
                                <button wire:click="editSemester({{ $sem->id }})" class="text-xs text-gray-500 hover:text-blue-600">
                                    {{ $sem->source_document_code ? 'Lihat' : 'Edit' }}
                                </button>
                                <button wire:click="openDuplicateSemesterModal({{ $sem->id }})" class="text-xs text-gray-500 hover:text-green-600">
                                    Duplikat
                                </button>
                                @if(!$sem->source_document_code)
                                    <button wire:click="confirmDeleteSemester({{ $sem->id }})" class="text-xs text-gray-500 hover:text-red-600">Hapus</button>
                                @endif
                            </div>'''
content = content.replace(old_semester_btns, new_semester_btns)

# Update Agenda Buttons + Checkboxes
old_agenda_header = '''                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>'''
new_agenda_header = '''                        <!-- Bulk Actions and CSV -->
                        <div class="p-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                <select wire:model="bulkAction" class="text-sm border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Aksi Massal --</option>
                                    <option value="publish">Jadikan Publik</option>
                                    <option value="unpublish">Jadikan Draft</option>
                                    <option value="duplicate">Duplikat</option>
                                    <option value="delete">Hapus</option>
                                </select>
                                <button wire:click="executeBulkAction(bulkAction)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1 px-3 rounded shadow">Terapkan</button>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="downloadCsvTemplate" class="text-xs text-blue-600 hover:underline">Download Template</button>
                                <input type="file" wire:model="csvFile" id="csvUpload" class="hidden" accept=".csv">
                                <label for="csvUpload" class="cursor-pointer bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded shadow">Import CSV</label>
                                <button wire:click="processCsvUpload" class="hidden" id="processCsvBtn"></button>
                                <button wire:click="exportEventsCsv" class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-1 px-3 rounded shadow">Export CSV</button>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">
                                        <input type="checkbox" wire:model.live="selectAllEvents" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>'''

content = content.replace(old_agenda_header, new_agenda_header)

old_agenda_row = '''                                @forelse($events as $event)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">'''
new_agenda_row = '''                                @forelse($events as $event)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <input type="checkbox" wire:model="selectedEvents" value="{{ $event->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">'''
content = content.replace(old_agenda_row, new_agenda_row)

old_agenda_actions = '''                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                            <button wire:click="editEvent({{ $event->id }})" class="text-indigo-600 hover:text-indigo-900 mr-2">
                                                {{ $event->is_source_locked ? 'Lihat' : 'Edit' }}
                                            </button>
                                            @if(!$event->is_source_locked)
                                                <button wire:click="deleteEvent({{ $event->id }})" onclick="confirm('Yakin ingin menghapus agenda ini?') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900">Hapus</button>
                                            @endif
                                        </td>'''
new_agenda_actions = '''                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium flex gap-2">
                                            <button wire:click="editEvent({{ $event->id }})" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $event->is_source_locked ? 'Lihat' : 'Edit' }}
                                            </button>
                                            <button wire:click="duplicateEvent({{ $event->id }})" class="text-green-600 hover:text-green-900">Duplikat</button>
                                            @if(!$event->is_source_locked)
                                                <button wire:click="confirmDeleteEvent({{ $event->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                                            @endif
                                        </td>'''
content = content.replace(old_agenda_actions, new_agenda_actions)

with open(r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\calendar-manager.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Done frontend phase 1')
