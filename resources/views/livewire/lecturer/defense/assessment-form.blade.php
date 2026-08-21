<div>
    @section('title', 'Penilaian Sidang')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Form Penilaian: {{ $role === 'supervisor' ? 'Dosen Pembimbing (F3)' : 'Dosen Penguji (F4)' }}
        </h3>
        <div>
            <a href="{{ route('lecturer.defenses.internship.my-defenses') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                &larr; Kembali
            </a>
        </div>
    </div>
    
    <div class="p-6 bg-gray-50 flex space-x-6">
        <!-- Kiri: Info & Dokumen -->
        <div class="w-1/3 space-y-6">
            <div class="bg-white shadow rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3 border-b pb-2">Informasi Mahasiswa</h4>
                <div class="text-sm text-gray-600 space-y-2">
                    <p><span class="font-semibold text-gray-800">Nama:</span> {{ $defenseCase->student->name }}</p>
                    <p><span class="font-semibold text-gray-800">NIM:</span> {{ $defenseCase->student->nim }}</p>
                    <p><span class="font-semibold text-gray-800">Judul:</span> {{ $defenseCase->metadata['report_title'] ?? '-' }}</p>
                    <p><span class="font-semibold text-gray-800">Perusahaan:</span> {{ $defenseCase->metadata['company_name'] ?? '-' }}</p>
                </div>
            </div>
            
            <div class="bg-white shadow rounded-lg p-4">
                <h4 class="font-medium text-gray-900 mb-3 border-b pb-2">Dokumen Mahasiswa</h4>
                <ul class="text-sm space-y-2">
                    @foreach($defenseCase->submission->files as $file)
                        <li>
                            <a href="/storage/{{ $file->path }}" target="_blank" class="text-indigo-600 hover:underline flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                {{ str_replace('_', ' ', Str::title($file->key)) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <!-- Kanan: Form Nilai -->
        <div class="w-2/3 bg-white shadow rounded-lg p-6">
            @if (session()->has('message'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                    <p class="text-sm text-green-700">{{ session('message') }}</p>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif
            
            @if($isFinal)
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4 flex items-center">
                    <svg class="h-5 w-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <p class="text-sm text-blue-700">Penilaian telah difinalisasi dan dikunci.</p>
                </div>
            @endif

            <div class="bg-indigo-50 p-4 rounded-md mb-6 flex justify-between items-center">
                <span class="text-indigo-800 font-semibold">Total Nilai</span>
                <span class="text-2xl font-bold text-indigo-900">{{ $totalScore }} <span class="text-sm font-normal text-indigo-600">/ 100</span></span>
            </div>
            
            <form>
                @foreach($rubricSections as $section)
                    <div class="mb-6">
                        <h4 class="font-bold text-gray-800 bg-gray-100 px-3 py-2 rounded">{{ $section->name }} (Maks {{ $section->max_score }})</h4>
                        <div class="mt-3 space-y-4 px-3">
                            @foreach($section->items as $item)
                                <div class="flex justify-between items-start pb-3 border-b border-gray-50 last:border-0">
                                    <div class="w-3/4 pr-4">
                                        <label class="block text-sm font-medium text-gray-700">{{ $item->name }}</label>
                                        <p class="text-xs text-gray-500 mt-1">{{ $item->description }}</p>
                                    </div>
                                    <div class="w-1/4 flex flex-col items-end">
                                        <input type="number" wire:model.live="scores.{{ $item->id }}" min="0" max="{{ $item->max_score }}" @if($isFinal) disabled @endif class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-right @if($isFinal) bg-gray-100 @endif" placeholder="Maks {{ $item->max_score }}">
                                        @error('scores.'.$item->id) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                @if($role === 'examiner')
                    <div class="mb-6 border-t pt-4">
                        <h4 class="font-bold text-gray-800 mb-2">Originalitas (Wajib)</h4>
                        <select wire:model="originality" @if($isFinal) disabled @endif class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @if($isFinal) bg-gray-100 @endif">
                            <option value="">-- Pilih Indikasi --</option>
                            <option value="Tidak Ada Indikasi Pelanggaran">Tidak Ada Indikasi Pelanggaran</option>
                            <option value="Perlu Pemeriksaan">Perlu Pemeriksaan</option>
                            <option value="Terbukti Plagiarisme">Terbukti Plagiarisme</option>
                        </select>
                        @if($originality === 'Terbukti Plagiarisme')
                            <p class="text-xs text-red-600 mt-2 font-semibold">PERINGATAN: Memilih "Terbukti Plagiarisme" akan mengakibatkan mahasiswa otomatis berstatus TIDAK LULUS berapapun nilai angkanya.</p>
                        @endif
                    </div>
                @endif
                
                @if(!$isFinal)
                <div class="mt-8 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" wire:click="saveDraft" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm">
                        Simpan Draft
                    </button>
                    <button type="button" wire:click="finalize" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:text-sm" onclick="confirm('Yakin ingin memfinalisasi? Nilai tidak dapat diubah setelah final. Tanda tangan elektronik Anda akan otomatis ditambahkan.') || event.stopImmediatePropagation()">
                        Finalisasi & Tanda Tangani
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>
