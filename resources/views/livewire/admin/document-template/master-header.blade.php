<div>
    @section('title', 'Kop Surat Master')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Kop Surat Master</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola kop surat institusi. Kop yang di-<em>Publish</em> dapat dipilih oleh Template Surat.</p>
        </div>
        <button wire:click="openCreateForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kop Surat
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <p class="text-sm text-green-700 font-medium">{{ session('message') }}</p>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 01-2 0V9zm1-4a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Create/Edit Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 z-40 flex items-start justify-center overflow-y-auto py-6" x-data>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl mx-4 my-auto">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white rounded-t-xl z-10">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit Draft Kop Surat' : 'Tambah Kop Surat Baru' }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Desain header dan footer kop surat bebas seperti Word.</p>
                </div>
                <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-5 max-h-[85vh] overflow-y-auto">
                {{-- Identifikasi --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Kop <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Kop Prodi Informatika">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Unik <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="code" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm font-mono" placeholder="kop_prodi_if">
                        @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit / Bagian</label>
                        <input type="text" wire:model="unit" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Program Studi Informatika">
                    </div>
                </div>

                {{-- Metadata Institusi (tersembunyi/opsional untuk search) --}}
                <div x-data="{ showMeta: false }">
                    <button type="button" @click="showMeta = !showMeta" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-4 h-4 transition-transform" :class="{'rotate-90': showMeta}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        Metadata Institusi (opsional — untuk referensi pencarian)
                    </button>
                    <div x-show="showMeta" x-transition class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 rounded-lg p-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Universitas</label>
                            <input type="text" wire:model="university_name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fakultas</label>
                            <input type="text" wire:model="faculty" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Program Studi</label>
                            <input type="text" wire:model="study_program" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat</label>
                            <input type="text" wire:model="campus_address" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Telepon</label>
                            <input type="text" wire:model="phone" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Website</label>
                            <input type="text" wire:model="website" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                    </div>
                </div>

                {{-- Upload Logo --}}
                <fieldset class="border border-gray-200 rounded-lg p-4">
                    <legend class="text-sm font-semibold text-gray-600 px-2">Upload Logo</legend>
                    <input type="file" wire:model="logo_upload" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-2">Format: PNG, JPG, WEBP, SVG. Maks 2MB. Anda juga bisa menyisipkan logo langsung di editor Header di bawah (drag & drop).</p>
                    @error('logo_upload') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    <div wire:loading wire:target="logo_upload" class="text-xs text-blue-600 mt-1 animate-pulse">Membaca file...</div>
                </fieldset>

                {{-- Header Editor (TinyMCE) --}}
                <fieldset class="border border-blue-200 rounded-lg p-4 bg-blue-50/30">
                    <legend class="text-sm font-semibold text-blue-700 px-2">🎨 Desain Header Kop Surat (Editor Bebas)</legend>
                    <p class="text-xs text-gray-500 mb-3">Desain header kop surat sebebas mungkin: ketik teks, atur font, sisipkan logo, atur alignment. Ini yang akan muncul di bagian atas setiap surat.</p>
                    <div wire:ignore>
                        <textarea id="tinymce-header">{!! $header_html !!}</textarea>
                    </div>
                </fieldset>

                {{-- Garis Pemisah --}}
                <fieldset class="border border-gray-200 rounded-lg p-4">
                    <legend class="text-sm font-semibold text-gray-600 px-2">Garis Pemisah Header</legend>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Gaya</label>
                            <select wire:model="separator_style" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                                <option value="solid">Garis Tunggal</option>
                                <option value="double">Garis Ganda</option>
                                <option value="none">Tanpa Garis</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Ketebalan (px)</label>
                            <input type="number" wire:model="separator_width" min="0" max="10" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Warna</label>
                            <input type="color" wire:model="separator_color" class="mt-1 h-9 w-full rounded-lg border-gray-300 cursor-pointer">
                        </div>
                    </div>
                    {{-- Preview Garis --}}
                    <div class="mt-3 flex items-center gap-2">
                        <span class="text-xs text-gray-500">Preview:</span>
                        <div class="flex-1" style="border-bottom: {{ $separator_width }}px {{ $separator_style }} {{ $separator_color }};"></div>
                    </div>
                </fieldset>

                {{-- Footer Editor (TinyMCE) --}}
                <fieldset class="border border-gray-200 rounded-lg p-4">
                    <legend class="text-sm font-semibold text-gray-600 px-2">Footer (opsional)</legend>
                    <div wire:ignore>
                        <textarea id="tinymce-footer">{!! $footer_html !!}</textarea>
                    </div>
                </fieldset>

                {{-- Margins --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Atas (mm)</label>
                        <input type="number" wire:model="margin_top" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Bawah (mm)</label>
                        <input type="number" wire:model="margin_bottom" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Kiri (mm)</label>
                        <input type="number" wire:model="margin_left" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Kanan (mm)</label>
                        <input type="number" wire:model="margin_right" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                </div>

                {{-- Catatan Perubahan --}}
                @if($editingId)
                <div>
                    <label class="block text-sm font-medium text-gray-700">Catatan Perubahan</label>
                    <input type="text" wire:model="change_notes" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Apa yang berubah?">
                </div>
                @endif

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" wire:click="$set('showForm', false)" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm transition-colors">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors flex items-center gap-2" wire:loading.attr="disabled">
                        <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span wire:loading.remove wire:target="save">Simpan</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Tabel Data (TANPA overflow-hidden) --}}
    <div class="bg-white shadow rounded-xl">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kop</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Unit</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Versi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($letterheads as $lh)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $lh->name }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $lh->university_name }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $lh->code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $lh->unit ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        v{{ $lh->activeVersion?->version_number ?? '?' }}
                        <span class="text-xs text-gray-400">({{ $lh->versions_count }} total)</span>
                    </td>
                    <td class="px-6 py-4">
                        @php $sc = ['draft'=>'bg-yellow-100 text-yellow-800','published'=>'bg-green-100 text-green-800','inactive'=>'bg-gray-100 text-gray-600','archived'=>'bg-red-100 text-red-800']; @endphp
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $sc[$lh->status] ?? 'bg-gray-100' }}">{{ ucfirst($lh->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div x-data="{ open: false }" class="relative inline-block text-left">
                            <button @click="open = !open" class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors" title="Aksi">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 @click.away="open = false" @keydown.escape.window="open = false"
                                 class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl ring-1 ring-black ring-opacity-10 z-[9999]">
                                <div class="py-1">
                                    <button wire:click="togglePreview({{ $lh->id }})" @click="open = false" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        {{ $previewingId === $lh->id ? 'Tutup Preview' : 'Preview' }}
                                    </button>
                                    <div class="border-t my-1"></div>
                                    @if($lh->status === 'draft')
                                        <button wire:click="editDraft({{ $lh->id }})" @click="open = false" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit Draft
                                        </button>
                                        <button wire:click="publish({{ $lh->id }})" @click="open = false" wire:confirm="Publish kop surat ini?" class="flex items-center w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50">
                                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Publish
                                        </button>
                                    @endif
                                    @if($lh->status === 'published')
                                        <button wire:click="createNewVersion({{ $lh->id }})" @click="open = false" class="flex items-center w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50">
                                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                            Buat Versi Baru
                                        </button>
                                        <button wire:click="deactivate({{ $lh->id }})" @click="open = false" wire:confirm="Nonaktifkan?" class="flex items-center w-full text-left px-4 py-2 text-sm text-orange-700 hover:bg-orange-50">
                                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Nonaktifkan
                                        </button>
                                    @endif
                                    <button wire:click="duplicate({{ $lh->id }})" @click="open = false" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        Duplikat
                                    </button>
                                    @if($lh->status !== 'archived')
                                        <button wire:click="archive({{ $lh->id }})" @click="open = false" wire:confirm="Arsipkan?" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12h12l1-12"/></svg>
                                            Arsipkan
                                        </button>
                                    @endif
                                    <div class="border-t my-1"></div>
                                    <button wire:click="deleteMaster({{ $lh->id }})" @click="open = false" wire:confirm="Hapus permanen kop surat ini?" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Hapus Permanen
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                {{-- Preview Row --}}
                @if($previewingId === $lh->id)
                <tr>
                    <td colspan="6" class="px-6 py-5 bg-blue-50 border-t border-blue-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-semibold text-blue-800">Preview: {{ $lh->name }}</span>
                            <button wire:click="togglePreview({{ $lh->id }})" class="text-xs text-blue-600 hover:underline">Tutup</button>
                        </div>
                        <div class="border border-gray-300 bg-white shadow-sm mx-auto rounded" style="max-width: 210mm; font-family: 'Times New Roman', Times, serif;">
                            {{-- Header HTML dari TinyMCE --}}
                            <div class="px-10 pt-6 pb-4" style="border-bottom: {{ $lh->activeVersion?->separator_width ?? 2 }}px {{ $lh->activeVersion?->separator_style === 'double' ? 'double' : ($lh->activeVersion?->separator_style ?? 'solid') }} {{ $lh->activeVersion?->separator_color ?? '#000' }};">
                                @if($lh->activeVersion?->header_html)
                                    {!! $lh->activeVersion->header_html !!}
                                @else
                                    {{-- Fallback: render dari structured data --}}
                                    <div class="flex items-center gap-4">
                                        @if($lh->activeVersion?->logoAsset?->activeVersion?->original_path)
                                            <img src="{{ asset('storage/' . $lh->activeVersion->logoAsset->activeVersion->original_path) }}" style="height: 70px; width: auto; flex-shrink: 0;">
                                        @endif
                                        <div class="flex-1 text-center">
                                            <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">{{ $lh->university_name }}</div>
                                            @if($lh->faculty)<div style="font-size: 11pt; font-weight: bold;">{{ $lh->faculty }}</div>@endif
                                            @if($lh->study_program)<div style="font-size: 10pt; font-weight: bold;">{{ $lh->study_program }}</div>@endif
                                            @if($lh->campus_address)<div style="font-size: 8pt; margin-top: 2px;">{{ $lh->campus_address }}</div>@endif
                                            @if($lh->phone || $lh->website)<div style="font-size: 8pt;">{{ $lh->phone }}{{ $lh->phone && $lh->website ? ' | ' : '' }}{{ $lh->website }}</div>@endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="px-10 py-8" style="min-height: 120px;">
                                <div style="color: #aaa; font-style: italic; text-align: center; font-size: 10pt; border: 1px dashed #ddd; padding: 20px; border-radius: 4px;">← Isi surat akan ditampilkan di sini →</div>
                            </div>
                            @if($lh->activeVersion?->footer_html)
                            <div class="px-10 pb-4" style="border-top: 1px solid #ccc; padding-top: 6px; font-size: 8pt; text-align: center; color: #555;">
                                {!! $lh->activeVersion->footer_html !!}
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endif

                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm text-gray-500">Belum ada kop surat. Klik <strong class="text-blue-600">Tambah Kop Surat</strong> untuk memulai.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script src="https://cdn.tiny.cloud/1/d80bciawk1t40y5crnx4t16lecfkpvb6yr3ejbt8qads4q6x/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    let headerEditor = null;
    let footerEditor = null;

    // Watch for Livewire showForm changes
    document.addEventListener('livewire:init', function() {
        Livewire.hook('morph.updated', ({ el }) => {
            // Reinit TinyMCE when form modal opens
            setTimeout(initKopEditors, 300);
        });
    });

    function initKopEditors() {
        // Only init when the elements exist in DOM
        if (!document.getElementById('tinymce-header')) return;

        // Destroy previous instances
        if (headerEditor) { tinymce.remove('#tinymce-header'); headerEditor = null; }
        if (footerEditor) { tinymce.remove('#tinymce-footer'); footerEditor = null; }

        // Header Editor
        tinymce.init({
            selector: '#tinymce-header',
            height: 300,
            menubar: false,
            promotion: false,
            branding: false,
            plugins: ['advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'searchreplace', 'visualblocks', 'code', 'table', 'wordcount'],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | image table | code',
            font_family_formats: "Times New Roman=times new roman,times,serif; Arial=arial,helvetica,sans-serif; Calibri=calibri,sans-serif; Georgia=georgia,serif; Verdana=verdana,sans-serif",
            font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 24pt',
            content_style: `body { font-family: 'Times New Roman', serif; font-size: 12pt; text-align: center; } table { border-collapse: collapse; width: 100%; } img { max-width: 100%; }`,
            paste_data_images: true,
            automatic_uploads: true,
            images_upload_handler: function(blobInfo) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    fetch('{{ route("admin.document-templates.upload-image") }}', { method: 'POST', body: formData, credentials: 'same-origin' })
                        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
                        .then(j => resolve(j.location))
                        .catch(e => reject('Upload gagal'));
                });
            },
            setup: function(editor) {
                editor.on('init', () => { headerEditor = editor; });
                editor.on('change keyup blur', () => { @this.set('header_html', editor.getContent()); });
            }
        });

        // Footer Editor
        tinymce.init({
            selector: '#tinymce-footer',
            height: 120,
            menubar: false,
            promotion: false,
            branding: false,
            plugins: ['autolink', 'lists', 'link'],
            toolbar: 'undo redo | fontsize | bold italic | alignleft aligncenter alignright',
            font_size_formats: '7pt 8pt 9pt 10pt',
            content_style: `body { font-family: 'Times New Roman', serif; font-size: 9pt; text-align: center; }`,
            setup: function(editor) {
                editor.on('init', () => { footerEditor = editor; });
                editor.on('change keyup blur', () => { @this.set('footer_html', editor.getContent()); });
            }
        });
    }

    // Init on page load (if form is already open)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initKopEditors, 500);
    });
    </script>
    @endpush
</div>
