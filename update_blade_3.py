import sys

with open(r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\calendar-manager.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add to Semester Modal
semester_fields_old = '''                        <div class="grid grid-cols-2 gap-4 mb-2">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>'''
semester_fields_new = '''                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status Publikasi</label>
                                <select wire:model="semester_publication_status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-6">
                                <input id="is_active" type="checkbox" wire:model="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $isSemesterLocked ? 'disabled' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 font-bold">
                                    Semester Aktif
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Internal (Opsional)</label>
                            <textarea wire:model="notes" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isSemesterLocked ? 'disabled' : '' }}></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-2">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>'''
content = content.replace(semester_fields_old, semester_fields_new)

# Add to Event Modal
event_fields_old = '''                        <div class="grid grid-cols-2 gap-4 mb-2">
                            <div class="flex items-center">
                                <input id="is_public" type="checkbox" wire:model="is_public" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $isEventLocked ? 'disabled' : '' }}>
                                <label for="is_public" class="ml-2 block text-sm text-gray-900 font-bold">
                                    Tampilkan Publik
                                </label>
                            </div>'''
event_fields_new = '''                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">URL Eksternal (Opsional)</label>
                            <input type="text" wire:model="external_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }} placeholder="https://...">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Internal (Opsional)</label>
                            <textarea wire:model="internal_notes" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status Publikasi</label>
                                <select wire:model="event_publication_status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" {{ $isEventLocked ? 'disabled' : '' }}>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-6">
                                <input id="is_public" type="checkbox" wire:model="is_public" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $isEventLocked ? 'disabled' : '' }}>
                                <label for="is_public" class="ml-2 block text-sm text-gray-900 font-bold">
                                    Tampilkan Publik
                                </label>
                            </div>'''
content = content.replace(event_fields_old, event_fields_new)

with open(r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\calendar-manager.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Done frontend phase 3')
