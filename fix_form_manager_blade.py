import sys
import re

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\livewire\admin\form-manager.blade.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add Kelola Semester button
pattern_add = r'<button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">\s*Tambah Form\s*</button>'
replacement_add = """<button wire:click="openSemesterModal" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-bold py-2 px-4 rounded mr-2 border border-indigo-300">
            Kelola Semester
        </button>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm">
            Tambah Form
        </button>"""
content = re.sub(pattern_add, replacement_add, content)

# 2. Fix filter buttons alignment
pattern_filters = r'<div class="w-full md:w-auto">\s*<button wire:click="clearFilters".*?</button>\s*</div>\s*<div class="w-full md:w-auto ml-auto">\s*<button wire:click="enableReorderMode".*?</button>\s*</div>'
replacement_filters = """<div class="w-full md:w-auto flex flex-col md:flex-row gap-2 mt-2 md:mt-0 md:ml-auto">
                <button wire:click="clearFilters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded w-full md:w-auto text-sm h-10 whitespace-nowrap">
                    Bersihkan Filter
                </button>
                <button wire:click="enableReorderMode" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded w-full md:w-auto text-sm h-10 whitespace-nowrap shadow-sm">
                    Atur Urutan
                </button>
            </div>"""
content = re.sub(pattern_filters, replacement_filters, content, flags=re.DOTALL)

# 3. Add Semester Modal at the end of file
semester_modal = """
    <!-- Modal Kelola Semester -->
    @if($isSemesterModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeSemesterModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            Manajemen Semester Akademik
                        </h3>
                        <button wire:click="closeSemesterModal" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Form Entry Semester -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded border border-gray-200">
                            <h4 class="font-bold text-sm text-gray-700 mb-3">{{ $semesterId ? 'Edit Semester' : 'Tambah Semester' }}</h4>
                            <form wire:submit.prevent="saveSemester">
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Semester</label>
                                    <input type="text" wire:model="sem_name" class="w-full text-sm border-gray-300 rounded shadow-sm" placeholder="Contoh: Gasal 2024/2025">
                                    @error('sem_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Jenis Semester</label>
                                    <select wire:model="sem_type" class="w-full text-sm border-gray-300 rounded shadow-sm">
                                        <option value="Gasal">Gasal</option>
                                        <option value="Genap">Genap</option>
                                        <option value="Antara">Semester Antara</option>
                                    </select>
                                    @error('sem_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Tahun Akademik</label>
                                    <input type="text" wire:model="sem_academic_year" class="w-full text-sm border-gray-300 rounded shadow-sm" placeholder="Contoh: 2024/2025">
                                    @error('sem_academic_year') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                    <input type="date" wire:model="sem_start_date" class="w-full text-sm border-gray-300 rounded shadow-sm">
                                    @error('sem_start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                                    <input type="date" wire:model="sem_end_date" class="w-full text-sm border-gray-300 rounded shadow-sm">
                                    @error('sem_end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3 flex items-center gap-4">
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" wire:model="sem_is_active" class="mr-1">
                                        Aktif
                                    </label>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-xs">Simpan</button>
                                    @if($semesterId)
                                        <button type="button" wire:click="resetSemesterFields" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-xs">Batal</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                        
                        <!-- List Semester -->
                        <div class="md:col-span-2">
                            <div class="overflow-y-auto max-h-[60vh]">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Semester</th>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Mulai</th>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Selesai</th>
                                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($calendars as $sem)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 text-sm text-gray-900 font-medium">
                                                {{ $sem->semester_name }}
                                                <div class="text-[10px] text-gray-500">{{ $sem->semester_type }} | {{ $sem->academic_year }}</div>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-500">{{ $sem->start_date ? $sem->start_date->format('d/m/Y') : '-' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-500">{{ $sem->end_date ? $sem->end_date->format('d/m/Y') : '-' }}</td>
                                            <td class="px-3 py-2 text-sm text-center">
                                                @if($sem->is_active)
                                                    <span class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                                @else
                                                    <span class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-gray-100 text-gray-800">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-sm font-medium text-center space-x-1">
                                                <button wire:click="editSemester({{ $sem->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded text-xs">Edit</button>
                                                <button wire:click="deleteSemester({{ $sem->id }})" onclick="confirm('Yakin ingin menghapus semester ini? Form yang terhubung harus dikosongkan dahulu.') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-1 rounded text-xs">Hapus</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
"""
content = re.sub(r'</div>\s*$', semester_modal, content)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
