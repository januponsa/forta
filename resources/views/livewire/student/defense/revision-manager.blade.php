<div>
    @section('title', 'Saran & Revisi Sidang')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Daftar Perbaikan / Saran Sidang
        </h3>
        <div>
            <a href="{{ route('student.defenses.internship.status') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                &larr; Kembali ke Status
            </a>
        </div>
    </div>
    
    <div class="p-6">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif
        
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($suggestions as $sug)
                <li>
                    <div class="px-4 py-5 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $sug->category }}
                            </p>
                            <div class="ml-2 flex-shrink-0 flex space-x-2">
                                @if($sug->priority === 'high')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Prioritas Tinggi</span>
                                @endif
                                
                                @if($sug->status === 'Sudah Diperbaiki')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Menunggu Verifikasi Dosen</span>
                                @elseif($sug->status === 'Disetujui')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Selesai / Disetujui</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $sug->status }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-700 bg-yellow-50 p-3 rounded border-l-4 border-yellow-400">
                            {{ $sug->suggestion }}
                        </div>
                        
                        @if($sug->student_response)
                            <div class="mt-4 bg-gray-50 p-3 rounded text-sm border-l-4 border-gray-300">
                                <span class="font-semibold text-gray-700">Tanggapan Anda:</span>
                                <p class="mt-1 text-gray-600">{{ $sug->student_response }}</p>
                            </div>
                        @endif
                        
                        <div class="mt-4 sm:flex sm:justify-between items-center">
                            <div class="sm:flex">
                                <p class="flex items-center text-xs text-gray-500">
                                    Dari: {{ $sug->lecturer->name }} ({{ $sug->role === 'supervisor' ? 'Pembimbing' : 'Penguji' }})
                                </p>
                            </div>
                            
                            @if($sug->status !== 'Disetujui')
                                <div class="mt-2 flex space-x-3 items-center text-sm sm:mt-0">
                                    <button wire:click="openModal({{ $sug->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium bg-indigo-50 px-3 py-1 rounded">
                                        Berikan Tanggapan / Laporan Revisi
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-4 py-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Belum ada saran atau perbaikan yang dicatat oleh dosen.
                </li>
                @endforelse
            </ul>
        </div>
    </div>
    
    <!-- Modal Form Tanggapan -->
    @if($isModalOpen)
    <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="saveResponse">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                            Laporkan Hasil Perbaikan
                        </h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Penjelasan Perbaikan / Tanggapan Anda</label>
                                <textarea wire:model="studentResponse" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Jelaskan secara detail apa yang telah Anda perbaiki sesuai saran dosen..."></textarea>
                                @error('studentResponse') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Kirim Tanggapan
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
