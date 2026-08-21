<div>
    @section('title', 'Manajemen Sidang - Input Nilai Mentor')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Input Nilai Mentor
        </h3>
        <div>
            <select wire:model.live="semesterFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                <option value="">Semua Semester</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem }}">{{ $sem }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="p-4">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mentor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Input</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cases as $case)
                        @php
                            $mentorAssessment = $case->assessments->first();
                        @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $case->student->name ?? 'Unknown' }}</div>
                            <div class="text-sm text-gray-500">{{ $case->student->nim ?? 'Unknown' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $case->metadata['mentor_name'] ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $case->metadata['company_name'] ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-lg font-bold text-gray-900">{{ $mentorAssessment ? $mentorAssessment->total_score : '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($mentorAssessment)
                                @if($mentorAssessment->status === 'final')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Final</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                @endif
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Belum Diinput</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="openModal({{ $case->id }})" class="text-indigo-600 hover:text-indigo-900">
                                {{ $mentorAssessment && $mentorAssessment->status === 'final' ? 'Lihat Nilai' : 'Input Nilai' }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Belum ada peserta sidang.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal Input Nilai Mentor (Split Screen) -->
    @if($isModalOpen)
    <div class="fixed z-10 inset-0 overflow-hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
        <div class="fixed inset-y-0 right-0 max-w-full flex">
            <div class="w-screen max-w-7xl">
                <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                    <div class="px-4 py-6 bg-gray-50 sm:px-6 flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900" id="modal-title">
                            Input Nilai Mentor: {{ $studentName }}
                        </h2>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="relative flex-1 flex flex-col md:flex-row">
                        <!-- Kiri: Preview Dokumen -->
                        <div class="w-full md:w-3/5 border-b md:border-b-0 md:border-r border-gray-200 p-4 bg-gray-100 flex flex-col overflow-y-auto">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-medium text-gray-700">Dokumen Penilaian Mentor</h3>
                                <div class="flex space-x-2">
                                    <a id="pdf-new-tab-btn" href="#" target="_blank" rel="noopener" class="hidden inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Buka di Tab Baru
                                    </a>
                                    <a id="pdf-download-btn" href="#" class="hidden inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                            
                            <div wire:ignore class="flex-1 w-full bg-white border border-gray-300 rounded overflow-hidden flex flex-col relative" id="pdf-viewer-container">
                                <!-- Toolbar -->
                                <div id="pdf-toolbar" class="hidden bg-gray-200 p-2 border-b border-gray-300 flex justify-between items-center text-sm">
                                    <div class="flex space-x-2">
                                        <button id="pdf-prev" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">&larr; Seb</button>
                                        <span class="flex items-center">Hal <span id="pdf-page-num" class="mx-1 font-bold">1</span> dari <span id="pdf-page-count" class="mx-1">?</span></span>
                                        <button id="pdf-next" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">Ber &rarr;</button>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button id="pdf-zoom-out" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">-</button>
                                        <button id="pdf-zoom-in" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">+</button>
                                        <button id="pdf-fit" class="px-2 py-1 bg-white border rounded shadow-sm hover:bg-gray-50">Fit</button>
                                    </div>
                                </div>
                                
                                <!-- Canvas Container -->
                                <div id="pdf-canvas-container" class="flex-1 overflow-auto bg-gray-100 flex justify-center p-4 relative">
                                    <canvas id="pdf-render-canvas"></canvas>
                                </div>

                                <!-- Loading / Error Overlays -->
                                <div id="pdf-loading" class="absolute inset-0 bg-white bg-opacity-90 flex flex-col justify-center items-center">
                                    <svg class="animate-spin h-8 w-8 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">Memuat PDF...</span>
                                </div>
                                <div id="pdf-error" class="hidden absolute inset-0 bg-white flex flex-col justify-center items-center p-6 text-center">
                                    <svg class="h-12 w-12 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <p id="pdf-error-text" class="text-sm text-gray-800 font-medium mb-4">Gagal memuat PDF.</p>
                                    <button id="pdf-retry-btn" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Coba Muat Ulang</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Kanan: Form Input -->
                        <div class="w-full md:w-2/5 p-6 overflow-y-auto">
                            <div class="bg-indigo-50 p-4 rounded-md mb-6 flex justify-between items-center">
                                <span class="text-indigo-800 font-semibold">Total Nilai Mentor (Rata-rata)</span>
                                <span class="text-2xl font-bold text-indigo-900">{{ $totalScore }}</span>
                            </div>
                            
                            <form>
                                @foreach($rubricItems as $item)
                                    <div class="mb-4 pb-4 border-b border-gray-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ $item->name }}
                                        </label>
                                        <p class="text-xs text-gray-500 mb-2">{{ $item->description }}</p>
                                        <div class="flex items-center">
                                            <input type="number" step="any" wire:model.live="scores.{{ $item->id }}" min="0" max="100" class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0-100">
                                            <span class="ml-2 text-xs text-gray-400">Rentang 1-100</span>
                                        </div>
                                    </div>
                                @endforeach
                            </form>
                            
                            <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                                <button type="button" wire:click="saveDraft" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                                    Simpan Draft
                                </button>
                                <button type="button" wire:click="finalize" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:text-sm" onclick="confirm('Yakin ingin memfinalisasi? Nilai tidak dapat diubah setelah final.') || event.stopImmediatePropagation()">
                                    Finalisasi
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
    @vite('resources/js/mentor-pdf-viewer.js')
@endpush
