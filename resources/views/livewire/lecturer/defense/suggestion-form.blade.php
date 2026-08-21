<div>
    @section('title', 'Saran & Revisi Sidang')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Form Saran & Revisi: {{ $defenseCase->student->name }}
        </h3>
        <div>
            <a href="{{ route('lecturer.defenses.internship.my-defenses') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                &larr; Kembali
            </a>
        </div>
    </div>
    
    <div class="p-6 bg-gray-50">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif
        
        <div class="mb-6 flex justify-between items-center">
            <h4 class="text-lg font-medium text-gray-800">Daftar Saran</h4>
            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Saran
            </button>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($suggestions as $sug)
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $sug->category }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex space-x-2">
                                @if($sug->priority === 'high')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tinggi</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Normal</span>
                                @endif
                                
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $sug->status }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-700">
                            {{ $sug->suggestion }}
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between items-center">
                            <div class="sm:flex">
                                <p class="flex items-center text-xs text-gray-500">
                                    Oleh: {{ $sug->lecturer->name }} ({{ $sug->role === 'supervisor' ? 'Pembimbing' : 'Penguji' }})
                                </p>
                            </div>
                            
                            @if($sug->lecturer_id === $lecturerId && $sug->role === $role)
                                <div class="mt-2 flex space-x-3 items-center text-sm sm:mt-0">
                                    <button wire:click="editSuggestion({{ $sug->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                                    <button wire:click="deleteSuggestion({{ $sug->id }})" class="text-red-600 hover:text-red-900 font-medium" onclick="confirm('Hapus saran ini?') || event.stopImmediatePropagation()">Hapus</button>
                                </div>
                            @endif
                        </div>
                        
                        @if($sug->student_response)
                            <div class="mt-4 bg-gray-50 p-3 rounded text-sm border-l-4 border-gray-300">
                                <span class="font-semibold text-gray-700">Tanggapan Mahasiswa:</span>
                                <p class="mt-1 text-gray-600">{{ $sug->student_response }}</p>
                            </div>
                        @endif
                    </div>
                </li>
                @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    Belum ada saran untuk mahasiswa ini.
                </li>
                @endforelse
            </ul>
        </div>
    </div>
    
    <!-- Modal Form Saran -->
    @if($isModalOpen)
    <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="saveSuggestion">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            {{ $editingId ? 'Edit Saran' : 'Tambah Saran Baru' }}
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select wire:model="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Isi Saran / Perbaikan</label>
                                <textarea wire:model="suggestion" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                @error('suggestion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Prioritas</label>
                                <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="normal">Normal</option>
                                    <option value="high">Tinggi (Wajib diperbaiki segera)</option>
                                </select>
                                @error('priority') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Simpan Saran
                        </button>
                        <button type="button" wire:click="$set('isModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
