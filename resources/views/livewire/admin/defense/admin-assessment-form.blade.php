<div>
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Input Nilai {{ ucfirst($role == 'supervisor' ? 'Pembimbing' : 'Penguji') }} (Admin Mode)
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Mahasiswa: {{ $studentName }} | Dosen: {{ $lecturerName }}
            </p>
        </div>
        <a href="{{ route('admin.defenses.internship.recap') }}" class="text-gray-400 hover:text-gray-500">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </a>
    </div>

    <div class="p-6">
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
        
        <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center mb-6">
            <span class="font-semibold text-blue-800">Total Nilai {{ ucfirst($role == 'supervisor' ? 'Pembimbing' : 'Penguji') }}</span>
            <span class="text-2xl font-bold text-blue-900">{{ $totalScore }}</span>
        </div>

        <form>
            @foreach($rubricSections as $section)
            <div class="mb-8">
                <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">{{ $section->name }} (Bobot: {{ $section->weight }}%)</h4>
                
                @foreach($section->items as $item)
                <div class="mb-4 flex flex-col sm:flex-row sm:justify-between sm:items-center bg-white p-4 shadow-sm rounded-lg border">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $item->description }}</label>
                        <p class="text-xs text-gray-500">Skor Max: {{ $item->max_score }}</p>
                    </div>
                    <div class="mt-2 sm:mt-0 sm:w-1/4">
                        <input type="number" step="0.01" min="0" max="{{ $item->max_score }}" 
                               wire:model.live="scores.{{ $item->id }}" 
                               @if($isFinal) disabled @endif
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm {{ $isFinal ? 'bg-gray-100' : '' }}">
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

            @if($role === 'examiner')
            <div class="mb-8">
                <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">Penilaian Originalitas (Wajib Penguji)</h4>
                <div class="bg-white p-4 shadow-sm rounded-lg border">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Status Originalitas</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="originality" value="Sangat Baik (Bebas Plagiasi)" class="form-radio text-indigo-600" @if($isFinal) disabled @endif>
                            <span class="ml-2 text-sm text-gray-700">Sangat Baik (Bebas Plagiasi)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="originality" value="Cukup (Plagiasi Ringan)" class="form-radio text-indigo-600" @if($isFinal) disabled @endif>
                            <span class="ml-2 text-sm text-gray-700">Cukup (Plagiasi Ringan)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model="originality" value="Buruk (Plagiasi Berat)" class="form-radio text-indigo-600" @if($isFinal) disabled @endif>
                            <span class="ml-2 text-sm text-gray-700">Buruk (Plagiasi Berat)</span>
                        </label>
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-8">
                <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">Tanda Tangan Dosen</h4>
                <div class="bg-white p-4 shadow-sm rounded-lg border">
                    @if($existingSignaturePath)
                    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded inline-block">
                        <p class="text-xs text-gray-500 mb-2 font-medium uppercase">Tanda Tangan Saat Ini:</p>
                        <img src="{{ Storage::url($existingSignaturePath) }}" alt="TTD" class="max-h-16 object-contain">
                    </div>
                    @else
                    <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded inline-block text-sm text-yellow-700">
                        Dosen ini belum memiliki tanda tangan di sistem.
                    </div>
                    @endif

                    @if(!$isFinal)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Upload/Ubah Tanda Tangan (.png transparan maks 2MB)</label>
                        <input type="file" wire:model="signature" class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-indigo-50 file:text-indigo-700
                            hover:file:bg-indigo-100
                        "/>
                        @error('signature') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="signature" class="mt-1 text-xs text-indigo-600">Mengunggah...</div>
                        <p class="text-xs text-gray-500 mt-2">Catatan: Mengunggah tanda tangan di sini akan memperbarui profil dosen secara permanen di seluruh sistem.</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                @if(!$isFinal)
                <button type="button" wire:click="saveDraft" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Simpan Draft
                </button>
                <button type="button" wire:click="finalize" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Submit Final
                </button>
                @else
                <button type="button" wire:click="unfinalize" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Buka Kunci (Unfinalize)
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
