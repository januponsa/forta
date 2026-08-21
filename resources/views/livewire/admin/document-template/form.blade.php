<div>
    @section('title', $isEditing ? 'Edit Template' : 'Buat Template Baru')

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

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.document-templates.index') }}" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Template
            </a>
            <h2 class="text-2xl font-semibold text-gray-800 mt-1">{{ $isEditing ? 'Edit: ' . $name : 'Buat Template Baru' }}</h2>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="previewTemplate()"
               class="px-4 py-2 border border-blue-300 text-blue-600 rounded-lg hover:bg-blue-50 text-sm font-medium transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Preview
            </button>
            <button wire:click="save"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors flex items-center gap-2"
                    wire:loading.attr="disabled">
                <svg wire:loading wire:target="save" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </div>

    {{-- Validation Error Summary --}}
    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm font-semibold text-red-800 mb-2">⚠ Harap perbaiki kesalahan berikut:</p>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li class="text-sm text-red-700">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="flex gap-6">
        {{-- Main Editor Area --}}
        <div class="flex-1 space-y-5 min-w-0">

            {{-- Informasi Dasar --}}
            <div class="bg-white shadow rounded-xl p-5 space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider border-b pb-2">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Template <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Surat Pengantar Magang 2025">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kode Template <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm font-mono" placeholder="Contoh: surat_magang_2025">
                        @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select wire:model="category" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            <option value="surat_magang">Surat Magang</option>
                            <option value="dokumen_sidang">Dokumen Sidang</option>
                            <option value="surat_umum">Surat Umum</option>
                            <option value="sertifikat">Sertifikat</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Peruntukan</label>
                        <select wire:model="document_purpose" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            @foreach($availablePurposes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @if($document_purpose && $document_purpose !== '')
                        <p class="text-xs text-blue-600 mt-1">✅ Setelah di-<em>Publish</em>, template ini otomatis digunakan saat generate dokumen <strong>{{ $availablePurposes[$document_purpose] ?? $document_purpose }}</strong>.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kop Surat</label>
                        <select wire:model.live="letterhead_version_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            <option value="">-- Tanpa Kop --</option>
                            @foreach($allLetterheadVersions as $lv)
                                <option value="{{ $lv->id }}">{{ $lv->master->name }} (v{{ $lv->version_number }})</option>
                            @endforeach
                        </select>
                        @if(!count($allLetterheadVersions))
                            <p class="text-xs text-orange-500 mt-1">⚠ Belum ada Kop Surat yang di-publish. Buat terlebih dahulu di menu Kop Surat Master.</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nomor Surat <span class="text-gray-400 font-normal text-xs">(opsional, bisa dioverride)</span></label>
                        <input type="text" wire:model="header_html" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm font-mono" placeholder="Contoh: 001/FORTA/INF/VII/2025">
                        <p class="text-xs text-gray-400 mt-1">Bisa diedit saat generate surat individual. Variabel: <code class="bg-gray-100 px-1 rounded text-[10px]">@{{ nomor_surat }}</code></p>
                    </div>
                </div>
            </div>

            {{-- Pengaturan Halaman --}}
            <div class="bg-white shadow rounded-xl p-5 space-y-3">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider border-b pb-2">Pengaturan Halaman</h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Kertas</label>
                        <select wire:model="paper_size" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            <option value="A4">A4</option>
                            <option value="Letter">Letter</option>
                            <option value="Legal">Legal</option>
                            <option value="F4">F4</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Orientasi</label>
                        <select wire:model="orientation" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Atas</label>
                        <input type="number" wire:model="margin_top" oninput="updateEditorMargins()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Bawah</label>
                        <input type="number" wire:model="margin_bottom" oninput="updateEditorMargins()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Kiri</label>
                        <input type="number" wire:model="margin_left" oninput="updateEditorMargins()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Margin Kanan</label>
                        <input type="number" wire:model="margin_right" oninput="updateEditorMargins()" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm text-sm">
                    </div>
                </div>
            </div>

            {{-- Body Surat — TinyMCE Editor --}}
            <div class="bg-white shadow rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3 border-b pb-2">
                    Body Surat
                    <span class="text-xs font-normal text-gray-400 ml-1 normal-case">Editor lengkap seperti Word. Drag gambar langsung ke editor, atau klik variabel di panel kanan.</span>
                </h3>
                <div wire:ignore>
                    <textarea id="tinymce-body">{!! $body_html !!}</textarea>
                </div>
            </div>

            {{-- Catatan Perubahan --}}
            @if($isEditing)
            <div class="bg-white shadow rounded-xl p-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Perubahan <span class="text-gray-400 font-normal">(opsional)</span></label>
                <input type="text" wire:model="change_notes" class="block w-full rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Apa yang berubah di versi ini?">
            </div>
            @endif
        </div>

        {{-- Right Sidebar: Placeholder & Variabel --}}
        <div class="w-72 flex-shrink-0">
            <div class="bg-white shadow rounded-xl p-4 sticky top-4 max-h-[calc(100vh-100px)] overflow-y-auto">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-1">Placeholder & Variabel</h3>
                <p class="text-xs text-gray-400 mb-4">Klik untuk menyisipkan ke editor.</p>

                @foreach($placeholders as $category => $items)
                <div x-data="{ open: true }" class="mb-3">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left text-sm font-semibold text-gray-600 hover:text-gray-900 py-1.5 border-b border-gray-100">
                        <span>{{ $category }}</span>
                        <svg class="w-3.5 h-3.5 transition-transform text-gray-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="space-y-1 mt-1.5">
                        @foreach($items as $code => $label)
                        <button type="button"
                                onclick="insertToEditor('{{ addslashes($code) }}')"
                                class="w-full text-left px-3 py-1.5 text-xs bg-blue-50 hover:bg-blue-100 text-blue-800 rounded-lg transition-colors border border-transparent hover:border-blue-200"
                                title="Klik untuk menyisipkan: {{ $code }}">
                            <span class="block font-medium">{{ $label }}</span>
                            <span class="block text-blue-400 font-mono text-[10px] mt-0.5">{{ $code }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach

                {{-- Tabel Dinamis --}}
                <div x-data="{ open: false }" class="mb-3">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left text-sm font-semibold text-purple-600 hover:text-purple-900 py-1.5 border-b border-gray-100">
                        <span>Tabel Dinamis</span>
                        <svg class="w-3.5 h-3.5 transition-transform text-purple-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="space-y-1 mt-1.5">
                        @foreach($dynamicTables as $code => $label)
                        <button type="button"
                                onclick="insertToEditor('{{ addslashes($code) }}')"
                                class="w-full text-left px-3 py-1.5 text-xs bg-purple-50 hover:bg-purple-100 text-purple-800 rounded-lg transition-colors border border-transparent hover:border-purple-200">
                            <span class="block font-medium">{{ $label }}</span>
                            <span class="block text-purple-400 font-mono text-[10px] mt-0.5">{{ $code }}</span>
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Penandatangan --}}
                @if(count($signatories) > 0)
                <div x-data="{ open: false }" class="mb-3">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left text-sm font-semibold text-green-600 hover:text-green-900 py-1.5 border-b border-gray-100">
                        <span>Penandatangan ({{ count($signatories) }})</span>
                        <svg class="w-3.5 h-3.5 transition-transform text-green-400" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" class="space-y-1 mt-1.5">
                        @foreach($signatories as $sig)
                        <div class="px-3 py-1.5 text-xs bg-green-50 text-green-800 rounded-lg border border-green-100">
                            <span class="block font-semibold">{{ $sig->name }}</span>
                            <span class="block text-green-500 mt-0.5">{{ $sig->position }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Panduan --}}
                <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <p class="text-xs font-semibold text-amber-800 mb-1.5">💡 Tips</p>
                    <ul class="text-xs text-amber-700 leading-relaxed space-y-1">
                        <li>• Drag-drop gambar langsung ke editor</li>
                        <li>• Klik variabel di atas untuk sisipkan data otomatis</li>
                        <li>• Nomor surat dapat diedit bebas</li>
                        <li>• Setelah Simpan → Publish agar aktif di sistem</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div id="preview-modal" class="fixed inset-0 bg-gray-900 bg-opacity-70 z-50 hidden items-start justify-center overflow-y-auto py-8" onclick="if(event.target===this)closePreview()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 my-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 rounded-t-xl">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Preview Template</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Tampilan perkiraan hasil akhir dokumen. Tekan <kbd class="bg-gray-200 px-1 rounded text-[10px]">Esc</kbd> untuk menutup.</p>
                </div>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-700 p-2 rounded-full hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-8 bg-gray-100">
                {{-- Paper simulation --}}
                <div id="preview-paper" class="bg-white shadow-lg mx-auto rounded border border-gray-200" style="max-width: 210mm; min-height: 297mm; font-family: 'Times New Roman', Times, serif; font-size: 12pt;">
                    {{-- Letterhead Preview --}}
                    <div id="preview-letterhead">
                        @if($letterheadPreview)
                            @if($letterheadPreview->header_html)
                                <div class="px-[25mm] pt-[15mm] pb-2">
                                    {!! $letterheadPreview->header_html !!}
                                </div>
                            @else
                                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: {{ $letterheadPreview->separator_width }}px {{ $letterheadPreview->separator_style }} {{ $letterheadPreview->separator_color }}; margin: 0 25mm 5px;" class="pt-[15mm] pb-[5mm]">
                                    <div style="width: 80px; text-align: center; flex-shrink: 0;">
                                        @if($letterheadPreview->logoAsset?->activeVersion)
                                            <img src="{{ asset('storage/' . $letterheadPreview->logoAsset->activeVersion->original_path) }}"
                                                 style="max-height: 70px; max-width: 80px; object-fit: contain;" alt="Logo">
                                        @endif
                                    </div>
                                    <div style="flex-grow: 1; text-align: center; padding: 0 15px;">
                                        <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase; line-height: 1.2;">{{ $letterheadPreview->master->university_name }}</div>
                                        @if($letterheadPreview->master->faculty)
                                            <div style="font-size: 13pt; font-weight: bold; line-height: 1.2; margin-top: 2px;">{{ $letterheadPreview->master->faculty }}</div>
                                        @endif
                                        @if($letterheadPreview->master->study_program)
                                            <div style="font-size: 11pt; font-weight: bold; line-height: 1.2; margin-top: 2px;">{{ $letterheadPreview->master->study_program }}</div>
                                        @endif
                                        <div style="font-size: 8.5pt; margin-top: 5px; color: #4b5563; line-height: 1.3;">
                                            {{ $letterheadPreview->master->campus_address }}<br>
                                            Telp: {{ $letterheadPreview->master->phone ?? '-' }} | Email: {{ $letterheadPreview->master->email ?? '-' }} | Web: {{ $letterheadPreview->master->website ?? '-' }}
                                        </div>
                                    </div>
                                    <div style="width: 80px; visibility: hidden; flex-shrink: 0;">
                                        <img src="" style="width: 80px;">
                                    </div>
                                </div>
                            @endif

                            @if($letterheadPreview->header_html)
                                {{-- Show separator for customized header --}}
                                <div style="border-bottom: {{ $letterheadPreview->separator_width }}px {{ $letterheadPreview->separator_style }} {{ $letterheadPreview->separator_color }}; margin: 0 25mm 15px;"></div>
                            @endif
                        @endif
                    </div>
                    {{-- Body Content --}}
                    <div id="preview-content" class="px-[25mm] py-[20mm]" style="line-height: 1.6;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- TinyMCE CDN --}}
    <script src="https://cdn.tiny.cloud/1/d80bciawk1t40y5crnx4t16lecfkpvb6yr3ejbt8qads4q6x/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
    let editorInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        initTinyMCE();
    });

    function updateEditorMargins() {
        if (!editorInstance) return;
        const mt = document.querySelector('[wire\\:model="margin_top"]')?.value || 25;
        const mb = document.querySelector('[wire\\:model="margin_bottom"]')?.value || 25;
        const ml = document.querySelector('[wire\\:model="margin_left"]')?.value || 25;
        const mr = document.querySelector('[wire\\:model="margin_right"]')?.value || 25;
        
        editorInstance.dom.setStyle(editorInstance.getBody(), 'padding', `${mt}mm ${mr}mm ${mb}mm ${ml}mm`);
        
        // Tambahkan garis panduan margin (visual guide) agar pengguna tahu batas kertas
        editorInstance.dom.setStyle(editorInstance.getBody(), 'background-image', 
            `linear-gradient(to right, transparent ${ml}mm, #cbd5e1 ${ml}mm, #cbd5e1 calc(${ml}mm + 1px), transparent calc(${ml}mm + 1px)), ` +
            `linear-gradient(to left, transparent ${mr}mm, #cbd5e1 ${mr}mm, #cbd5e1 calc(${mr}mm + 1px), transparent calc(${mr}mm + 1px)), ` +
            `linear-gradient(to bottom, transparent ${mt}mm, #cbd5e1 ${mt}mm, #cbd5e1 calc(${mt}mm + 1px), transparent calc(${mt}mm + 1px)), ` +
            `linear-gradient(to top, transparent ${mb}mm, #cbd5e1 ${mb}mm, #cbd5e1 calc(${mb}mm + 1px), transparent calc(${mb}mm + 1px))`
        );
        editorInstance.dom.setStyle(editorInstance.getBody(), 'background-size', '100% 100%');
        editorInstance.dom.setStyle(editorInstance.getBody(), 'background-repeat', 'no-repeat');
    }

    function initTinyMCE() {
        if (editorInstance) {
            tinymce.remove('#tinymce-body');
            editorInstance = null;
        }

        tinymce.init({
            selector: '#tinymce-body',
            height: 700,
            menubar: 'file edit view insert format table',
            promotion: false,
            branding: false,

            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount',
                'pagebreak', 'emoticons'
            ],

            toolbar: [
                'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor',
                'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table image link pagebreak | removeformat code fullscreen'
            ].join(' | '),

            font_family_formats: "Times New Roman=times new roman,times,serif; Arial=arial,helvetica,sans-serif; Courier New=courier new,courier,monospace; Georgia=georgia,serif; Verdana=verdana,geneva,sans-serif; Tahoma=tahoma,arial,helvetica,sans-serif; Calibri=calibri,sans-serif",

            font_size_formats: '8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 24pt 28pt 36pt 48pt',

            content_style: `
                html {
                    background-color: #f3f4f6;
                    padding: 20px 0;
                }
                body {
                    font-family: 'Times New Roman', Times, serif;
                    font-size: 12pt;
                    line-height: 1.6;
                    background-color: #ffffff;
                    width: 210mm;
                    min-height: 297mm;
                    margin: 0 auto;
                    box-sizing: border-box;
                    padding: 25mm 25mm 25mm 25mm;
                    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
                    border: 1px solid #e5e7eb;
                }
                table { border-collapse: collapse; width: 100%; }
                table td, table th { border: 1px solid #ccc; padding: 6px 10px; }
                img { max-width: 100%; height: auto; }
            `,

            // Image Upload via server
            images_upload_url: '{{ route("admin.document-templates.upload-image") }}',
            images_upload_credentials: true,
            automatic_uploads: true,
            images_reuse_filename: true,
            paste_data_images: true,

            // CSRF Token for uploads
            images_upload_handler: function(blobInfo) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                    fetch('{{ route("admin.document-templates.upload-image") }}', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin',
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Upload gagal: ' + response.status);
                        return response.json();
                    })
                    .then(json => {
                        resolve(json.location);
                    })
                    .catch(err => {
                        reject('Upload gambar gagal: ' + err.message);
                    });
                });
            },

            // File picker for images
            file_picker_types: 'image',
            file_picker_callback: function(callback, value, meta) {
                if (meta.filetype === 'image') {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');
                    input.addEventListener('change', function() {
                        const file = this.files[0];
                        const formData = new FormData();
                        formData.append('file', file);
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                        fetch('{{ route("admin.document-templates.upload-image") }}', {
                            method: 'POST',
                            body: formData,
                            credentials: 'same-origin',
                        })
                        .then(res => res.json())
                        .then(json => {
                            callback(json.location, { title: file.name });
                        })
                        .catch(() => alert('Upload gagal'));
                    });
                    input.click();
                }
            },

            // Sync ke Livewire saat konten berubah
            setup: function(editor) {
                editor.on('init', function() {
                    editorInstance = editor;
                    updateEditorMargins();
                });

                editor.on('change keyup', function() {
                    @this.set('body_html', editor.getContent());
                });

                editor.on('blur', function() {
                    @this.set('body_html', editor.getContent());
                });
            },

            // Table settings
            table_default_styles: { 'border-collapse': 'collapse', 'width': '100%' },
            table_default_attributes: { border: '1' },
            table_responsive_width: true,

            // Indonesian language (fallback to English if not available)
            language: 'id',
            language_url: '',
        });
    }

    // Insert placeholder/variable ke TinyMCE
    function insertToEditor(code) {
        if (editorInstance) {
            editorInstance.insertContent(code);
            editorInstance.focus();
        }
    }

    // Preview
    function previewTemplate() {
        const modal = document.getElementById('preview-modal');
        const content = document.getElementById('preview-content');

        // Set content from editor
        if (editorInstance) {
            content.innerHTML = editorInstance.getContent();
        }

        // Apply margin settings from form to the preview body
        const mt = document.querySelector('[wire\\:model="margin_top"]')?.value || 25;
        const mb = document.querySelector('[wire\\:model="margin_bottom"]')?.value || 25;
        const ml = document.querySelector('[wire\\:model="margin_left"]')?.value || 25;
        const mr = document.querySelector('[wire\\:model="margin_right"]')?.value || 25;
        content.style.padding = `${mt}mm ${mr}mm ${mb}mm ${ml}mm`;

        // Show the number/letter ref if set
        const headerInput = document.querySelector('[wire\\:model=header_html]');
        const refDivId = 'preview-ref-number';
        let refDiv = document.getElementById(refDivId);
        if (headerInput && headerInput.value) {
            if (!refDiv) {
                refDiv = document.createElement('div');
                refDiv.id = refDivId;
                refDiv.style.cssText = 'margin-bottom: 16px; font-size: 11pt;';
            }
            refDiv.innerHTML = '<strong>Nomor:</strong> ' + headerInput.value;
            if (content.firstChild && content.firstChild.id !== refDivId) {
                content.insertBefore(refDiv, content.firstChild);
            } else if (!content.firstChild) {
                content.appendChild(refDiv);
            }
        } else if (refDiv) {
            refDiv.remove();
        }

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closePreview() {
        document.getElementById('preview-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    // Escape key to close preview
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('preview-modal');
            if (modal && modal.style.display === 'flex') {
                closePreview();
            }
        }
    });

    // Reinit jika Livewire me-refresh halaman
    document.addEventListener('livewire:navigated', () => {
        setTimeout(initTinyMCE, 200);
    });
    </script>
    @endpush
</div>
