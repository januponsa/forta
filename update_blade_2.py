import sys

with open(r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\calendar-manager.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the end of the file to include the new modals
new_modals = '''
    <!-- Modal Confirm Delete Semester -->
    @if($isDeleteSemesterModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isDeleteSemesterModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2 text-red-600">Konfirmasi Hapus Semester</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Terdapat <strong>{{ $deletingSemesterHasEvents ? 'beberapa' : '0' }}</strong> agenda pada semester ini. Apa yang ingin Anda lakukan?
                    </p>
                    <div class="space-y-3">
                        <button wire:click="executeDeleteSemester('archive')" class="w-full flex justify-center items-center px-4 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                            Arsipkan Semester Saja
                        </button>
                        @if(auth()->user()->role === 'admin_forta')
                        <button wire:click="executeDeleteSemester('force_delete')" class="w-full flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                            Hapus Permanen beserta Seluruh Agenda
                        </button>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="$set('isDeleteSemesterModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Confirm Delete Event -->
    @if($isDeleteEventModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isDeleteEventModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Agenda</h3>
                    <p class="text-sm text-gray-500">Anda yakin ingin menghapus agenda ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="executeDeleteEvent" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                    <button type="button" wire:click="$set('isDeleteEventModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Duplicate Semester -->
    @if($isDuplicateSemesterModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isDuplicateSemesterModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="executeDuplicateSemester">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Duplikat Semester</h3>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Semester Baru</label>
                            <input type="text" wire:model="dupSemesterName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('dupSemesterName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Akademik</label>
                            <input type="text" wire:model="dupAcademicYear" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai Baru</label>
                            <input type="date" wire:model="dupStartDate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('dupStartDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-1">Semua agenda akan digeser secara proporsional dari tanggal mulai baru ini. Semua agenda yang diduplikasi akan berstatus draft.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Duplikat</button>
                        <button type="button" wire:click="$set('isDuplicateSemesterModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal CSV Preview -->
    @if($isCsvPreviewModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isCsvPreviewModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Preview Import CSV</h3>
                    
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Mode Import jika Duplikat / Error</label>
                        <select wire:model="csvImportMode" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-white">
                            <option value="skip">Lewati (Abaikan data duplikat/error)</option>
                            <option value="update">Update (Timpa data yang sudah ada)</option>
                            <option value="abort">Batalkan (Berhenti jika ada error)</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Agenda</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tgl Mulai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($csvPreviewData as $row)
                                    <tr class="{{ $row['_status'] == 'error' ? 'bg-red-50' : '' }}">
                                        <td class="px-2 py-2 whitespace-nowrap text-xs">
                                            @if($row['_status'] == 'error')
                                                <span class="text-red-600 font-bold" title="{{ $row['_message'] }}">Error</span>
                                            @else
                                                <span class="text-green-600 font-bold">Valid</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-xs">{{ $row['semester_code'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-xs">{{ $row['title'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-xs">{{ $row['start_date'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="confirmCsvImport" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Mulai Import</button>
                    <button type="button" wire:click="$set('isCsvPreviewModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
'''

content = content.replace('</div>\n', new_modals + '\n</div>\n')

with open(r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\calendar-manager.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)
print('Done frontend phase 2')
