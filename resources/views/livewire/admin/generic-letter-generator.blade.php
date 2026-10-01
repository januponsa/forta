<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Buat Surat Manual</h2>
        <p class="text-sm text-gray-600 mt-1">Gunakan template dari Document Builder untuk menghasilkan berbagai macam surat (Internal Memo, Peminjaman, dll).</p>
    </div>

    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="rounded-md bg-red-50 p-4 mb-6 border-l-4 border-red-500">
            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Kolom Kiri -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Pemilihan Template</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label for="template" class="block text-sm font-medium text-gray-700">Pilih Template Surat</label>
                        <select wire:model.live="selectedTemplateId" id="template" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">-- Pilih Template --</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }} ({{ $template->document_purpose }})</option>
                            @endforeach
                        </select>
                        @error('selectedTemplateId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            @if($selectedTemplateId)
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Isi Konten Surat (Dinamis)</h3>
                    <p class="mt-1 text-sm text-gray-500">Form di bawah ini otomatis di-generate berdasarkan variabel <code>@{{ nama_variabel }}</code> yang ada di template.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($placeholders as $placeholder)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $placeholder) }}</label>
                            <input type="text" wire:model="formData.{{ $placeholder }}" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                        </div>
                    @empty
                        <div class="col-span-2 text-sm text-gray-500">
                            Template ini tidak memiliki variabel dinamis (placeholder).
                        </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Meta Logbook Kanan -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Metadata Surat (Logbook)</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Surat <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="letter_number" placeholder="Misal: 001/FT/2026" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                        @error('letter_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul/Perihal Surat <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="letter_title" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                        @error('letter_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Penerima (Opsional)</label>
                        <input type="text" wire:model="recipient_name" placeholder="Misal: PT. Maju Mundur" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="letter_date" class="mt-1 block w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-gray-300 rounded-md">
                        @error('letter_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <button wire:click="generateLetter" wire:loading.attr="disabled" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                            <span wire:loading.remove wire:target="generateLetter">Generate Surat & Simpan ke Logbook</span>
                            <span wire:loading wire:target="generateLetter">Sedang memproses...</span>
                        </button>
                    </div>

                    @if($generatedPdfUrl)
                    <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md text-center">
                        <p class="text-sm text-green-800 mb-2 font-medium">Surat berhasil dibuat!</p>
                        <a href="{{ $generatedPdfUrl }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Unduh Dokumen PDF
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
