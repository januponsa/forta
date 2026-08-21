<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengaturan Template Surat Magang</h2>
            <p class="text-sm text-gray-600 mt-1">Konfigurasikan format, kop surat, isi paragraf standar, dan tata letak PDF.</p>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-md bg-red-50 p-4 mb-6 border-l-4 border-red-500">
            <p class="text-sm font-medium text-red-800">Mohon perbaiki kesalahan berikut sebelum menyimpan:</p>
            <ul class="list-disc list-inside mt-2 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Informasi Kampus & Kop -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Kop Surat & Meta</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Universitas</label>
                    <input type="text" wire:model="university_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Kampus</label>
                    <input type="text" wire:model="campus_address" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Info Kontak (Telp/Web)</label>
                    <input type="text" wire:model="contact_info" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota Penerbitan</label>
                    <input type="text" wire:model="city" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Penomoran -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Format Penomoran</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Format Nomor Surat</label>
                    <input type="text" wire:model="number_format" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Gunakan tag: {nomor_urut}, {kode_surat}, {bulan_romawi}, {tahun}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Surat Internal</label>
                    <input type="text" wire:model="letter_code" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Paragraf Isi -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Konten Surat</h3>
            </div>
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perihal (Subject)</label>
                    <input type="text" wire:model="subject" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paragraf Pembuka</label>
                    <textarea wire:model="opening_paragraph" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Tersedia placeholder: \{\{program_studi\}\}, \{\{nama_mahasiswa\}\}, \{\{nim\}\}, \{\{semester\}\}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paragraf Inti/Tujuan</label>
                    <textarea wire:model="purpose_paragraph" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Tersedia placeholder: \{\{durasi\}\}, \{\{tanggal_mulai\}\}, \{\{tanggal_selesai\}\}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paragraf Penutup</label>
                    <textarea wire:model="closing_paragraph" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan, Logo & Footer -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Aset & Penandatangan</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Logo Universitas (dari Manajemen Aset)</label>
                    <select wire:model="logo_asset_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Pilih Logo --</option>
                        @foreach($availableLogos as $logo)
                            <option value="{{ $logo->id }}">{{ $logo->name }}</option>
                        @endforeach
                    </select>
                    @error('logo_asset_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Penandatangan (dari Manajemen Dosen)</label>
                    <select wire:model="lecturer_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">-- Pilih Penandatangan --</option>
                        @foreach($availableLecturers as $lecturer)
                            <option value="{{ $lecturer->id }}">{{ $lecturer->name }} - {{ $lecturer->position }}</option>
                        @endforeach
                    </select>
                    @error('lecturer_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Margin Kertas -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Margin Dokumen PDF (mm)</h3>
            </div>
            <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas</label>
                    <input type="number" wire:model="margin_top" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bawah</label>
                    <input type="number" wire:model="margin_bottom" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kiri</label>
                    <input type="number" wire:model="margin_left" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kanan</label>
                    <input type="number" wire:model="margin_right" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="inline-flex justify-center items-center rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
