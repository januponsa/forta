import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\defense\schedule-manager.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

replacement = """    <!-- Modal Jadwal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xl mx-auto flex flex-col max-h-full">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Penjadwalan: {{ $studentName }}
                </h3>
                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form wire:submit.prevent="saveSchedule" class="flex-1 overflow-y-auto">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Sidang</label>
                        <input type="date" wire:model="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                            <input type="time" wire:model="start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('start_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                            <input type="time" wire:model="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('end_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ruang / Link Daring</label>
                        <input type="text" wire:model="room_or_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('room_or_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <hr class="my-4 border-gray-200">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dosen Pembimbing</label>
                        <select wire:model="supervisor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Pilih Pembimbing --</option>
                            @foreach($lecturers as $lec)
                                <option value="{{ $lec->id }}">{{ $lec->name }}</option>
                            @endforeach
                        </select>
                        @error('supervisor_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dosen Penguji / Ketua</label>
                        <select wire:model="examiner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Pilih Penguji --</option>
                            @foreach($lecturers as $lec)
                                <option value="{{ $lec->id }}">{{ $lec->name }}</option>
                            @endforeach
                        </select>
                        @error('examiner_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif"""

import re
pattern = re.compile(r'<!-- Modal Jadwal -->.*?</div>\s*</div>\s*</div>\s*@endif', re.DOTALL)
content = pattern.sub(replacement, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
