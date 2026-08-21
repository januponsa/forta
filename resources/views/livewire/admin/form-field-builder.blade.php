<div class="space-y-6" x-data="formBuilder">
    @section('title', 'Builder Form: ' . $form->title)

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <style>
        .custom-builder-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .custom-builder-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
            .custom-builder-sidebar {
                grid-column: span 1 / span 4;
            }
            .custom-builder-canvas {
                grid-column: span 3 / span 4;
            }
        }
        /* Make sure fields don't overflow */
        .field-card-container {
            width: 100%;
            word-break: break-word;
        }
    </style>

    <!-- Top Action Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.forms') }}" class="text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">&larr; Kembali</a>
            <div>
                <h2 class="text-lg font-bold text-slate-800">{{ $form->title }}</h2>
                <div class="flex items-center space-x-2 mt-0.5">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-700 uppercase">{{ $form->phase }}</span>
                    <span class="text-xs text-slate-400">Semester {{ $form->semester }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center space-x-3 flex-wrap">
            <!-- Draft Autosave Indicator -->
            <div class="flex items-center space-x-1.5 text-xs text-slate-500 mr-2">
                <span class="h-2 w-2 rounded-full {{ $saveStatus === 'Tersimpan' ? 'bg-green-500' : ($saveStatus === 'Menyimpan...' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                <span>{{ $saveStatus }}</span>
            </div>

            <button wire:click="saveDraft" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2 px-4 rounded-lg text-sm shadow-sm transition-colors">
                Simpan Draft
            </button>
            <button wire:click="$set('isPreviewModalOpen', true)" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold py-2 px-4 rounded-lg text-sm transition-colors flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                Preview Form
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg relative shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="custom-builder-grid">
        
        <!-- LEFT PANEL: Templates, Section Manager, Bank -->
        <div class="space-y-6 custom-builder-sidebar">
            
            <!-- Quick Suggestion Templates -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 .364l-.707 .707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    Saran Pertanyaan Cepat
                </h3>
                <p class="text-xs text-slate-500 mb-4">Gunakan template siap pakai sesuai kategori pendaftaran kegiatan.</p>
                <div class="space-y-2">
                    <button wire:click="applyTemplate('pendaftaran_kegiatan')" onclick="confirm('Perhatian! Menerapkan template akan menghapus semua field saat ini. Lanjutkan?') || event.stopImmediatePropagation()" class="w-full text-left bg-slate-50 hover:bg-teal-50 hover:text-teal-700 p-2.5 rounded-lg text-xs font-semibold transition-all border border-slate-200">
                        1. Pendaftaran Kegiatan
                    </button>
                    <button wire:click="applyTemplate('pelaporan_kegiatan')" onclick="confirm('Perhatian! Menerapkan template akan menghapus semua field saat ini. Lanjutkan?') || event.stopImmediatePropagation()" class="w-full text-left bg-slate-50 hover:bg-teal-50 hover:text-teal-700 p-2.5 rounded-lg text-xs font-semibold transition-all border border-slate-200">
                        2. Pelaporan Kegiatan
                    </button>
                    <button wire:click="applyTemplate('tugas_akhir')" onclick="confirm('Perhatian! Menerapkan template akan menghapus semua field saat ini. Lanjutkan?') || event.stopImmediatePropagation()" class="w-full text-left bg-slate-50 hover:bg-teal-50 hover:text-teal-700 p-2.5 rounded-lg text-xs font-semibold transition-all border border-slate-200">
                        3. Proposal Tugas Akhir
                    </button>
                    <button wire:click="applyTemplate('kkn')" onclick="confirm('Perhatian! Menerapkan template akan menghapus semua field saat ini. Lanjutkan?') || event.stopImmediatePropagation()" class="w-full text-left bg-slate-50 hover:bg-teal-50 hover:text-teal-700 p-2.5 rounded-lg text-xs font-semibold transition-all border border-slate-200">
                        4. Pendaftaran KKN
                    </button>
                    <button wire:click="applyTemplate('magang')" onclick="confirm('Perhatian! Menerapkan template akan menghapus semua field saat ini. Lanjutkan?') || event.stopImmediatePropagation()" class="w-full text-left bg-slate-50 hover:bg-teal-50 hover:text-teal-700 p-2.5 rounded-lg text-xs font-semibold transition-all border border-slate-200">
                        5. Pendaftaran Magang
                    </button>
                    <button wire:click="applyTemplate('konversi_mata_kuliah')" onclick="confirm('Perhatian! Menerapkan template akan menghapus semua field saat ini. Lanjutkan?') || event.stopImmediatePropagation()" class="w-full text-left bg-slate-50 hover:bg-teal-50 hover:text-teal-700 p-2.5 rounded-lg text-xs font-semibold transition-all border border-slate-200">
                        6. Konversi Mata Kuliah
                    </button>
                </div>
            </div>

            <!-- Sections List -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Daftar Section
                    </h3>
                    <button wire:click="openSectionModal()" class="text-xs text-blue-600 hover:text-blue-800 font-bold">+ Baru</button>
                </div>
                <div class="space-y-2" id="sections-list">
                    @forelse($form->sections as $sec)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs" data-id="{{ $sec->id }}">
                            <div class="flex items-center">
                                <span class="section-drag-handle cursor-move text-slate-400 mr-2 hover:text-slate-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-3.999A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"></path></svg>
                                </span>
                                <span class="font-medium text-slate-800 truncate max-w-[120px]">{{ $sec->order }}. {{ $sec->title }}</span>
                            </div>
                            <div class="flex items-center space-x-1 shrink-0">
                                <button wire:click="moveSectionUp({{ $sec->id }})" class="p-1 text-slate-400 hover:text-slate-600">&uarr;</button>
                                <button wire:click="moveSectionDown({{ $sec->id }})" class="p-1 text-slate-400 hover:text-slate-600">&darr;</button>
                                <button wire:click="openSectionModal({{ $sec->id }})" class="p-1 text-blue-500 hover:text-blue-700">Edit</button>
                                <button wire:click="deleteSection({{ $sec->id }})" onclick="confirm('Hapus section ini? Pertanyaan di dalamnya akan dipindahkan ke luar section.') || event.stopImmediatePropagation()" class="p-1 text-red-500 hover:text-red-700">x</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada section/halaman baru.</p>
                    @endforelse
                </div>
            </div>

            <!-- Question Bank Button -->
            <button wire:click="openBankModal" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold py-2.5 px-4 rounded-xl text-sm transition-colors border border-slate-200 flex items-center justify-center">
                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20"></path></svg>
                Bank Pertanyaan
            </button>
        </div>

        <!-- CENTER PANEL: Form Builder Canvas -->
        <div class="custom-builder-canvas space-y-6">
            
            <!-- General Fields (Without Section) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Bagian Utama / Global</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pertanyaan yang akan muncul pertama kali pada form.</p>
                    </div>
                    <button wire:click="openFieldModal(null, null)" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition-colors">
                        + Tambah Pertanyaan
                    </button>
                </div>

                <div class="space-y-4 min-h-[50px]" x-init="initFieldSortable($el, null)" data-section-id="">
                    @forelse($form->fields->whereNull('section_id') as $field)
                        @include('livewire.admin.partials.field-card', ['field' => $field])
                    @empty
                        <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-xl">
                            <p class="text-sm text-slate-400">Belum ada pertanyaan di bagian global.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Sections & Their Fields -->
            @foreach($form->sections as $section)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-start border-b border-slate-100 pb-4 mb-4">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-0.5 rounded">Halaman {{ $section->order }}</span>
                                <h3 class="text-base font-bold text-slate-800">{{ $section->title }}</h3>
                            </div>
                            @if($section->description)
                                <p class="text-xs text-slate-500 mt-1">{{ $section->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <button wire:click="openFieldModal(null, {{ $section->id }})" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-1.5 px-3 rounded-lg transition-colors">
                                + Tambah Pertanyaan
                            </button>
                            <button wire:click="duplicateSection({{ $section->id }})" class="text-slate-500 hover:text-slate-700 text-xs font-semibold py-1.5 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                                Salin Halaman
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4 min-h-[50px]" x-init="initFieldSortable($el, {{ $section->id }})" data-section-id="{{ $section->id }}">
                        @forelse($form->fields->where('section_id', $section->id) as $field)
                            @include('livewire.admin.partials.field-card', ['field' => $field])
                        @empty
                            <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-xl">
                                <p class="text-sm text-slate-400">Belum ada pertanyaan di halaman ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- FIELD EDITOR MODAL -->
    @if($isFieldModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-60" wire:click="closeFieldModal"></div>

        <div class="relative z-10 bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-2xl">
                <form wire:submit.prevent="saveField">
                    <div class="bg-white px-6 pt-6 pb-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-md font-bold text-slate-900">
                            {{ $fieldId ? 'Edit Pertanyaan' : 'Tambah Pertanyaan Baru' }}
                        </h3>
                        <button type="button" wire:click="closeFieldModal" class="text-slate-400 hover:text-slate-600 font-bold">x</button>
                    </div>

                    <div class="bg-white px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        <!-- Label -->
                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1.5">Label Pertanyaan / Label Input</label>
                            <input type="text" wire:model="label" placeholder="Tuliskan label pertanyaan..." class="w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('label') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type & Position Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-1.5">Tipe Elemen</label>
                                <select wire:model.live="type" class="w-full bg-white border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <optgroup label="Teks">
                                        <option value="text">Jawaban Singkat</option>
                                        <option value="textarea">Paragraf</option>
                                        <option value="email">Email</option>
                                        <option value="tel">Nomor Telepon</option>
                                        <option value="number">Angka</option>
                                        <option value="url">URL / Link</option>
                                    </optgroup>
                                    <optgroup label="Pilihan">
                                        <option value="radio">Pilihan Ganda (Radio)</option>
                                        <option value="checkbox">Kotak Centang (Checkboxes)</option>
                                        <option value="select">Dropdown (Select)</option>
                                        <option value="linear_scale">Skala Linear</option>
                                    </optgroup>
                                    <optgroup label="Tanggal & Waktu">
                                        <option value="date">Tanggal</option>
                                        <option value="time">Waktu</option>
                                        <option value="date_range">Rentang Tanggal</option>
                                    </optgroup>
                                    <optgroup label="File Upload">
                                        <option value="file">Unggah Berkas/File</option>
                                    </optgroup>
                                    <optgroup label="Struktural / Informasi">
                                        <option value="section_title">Judul Bagian</option>
                                        <option value="info">Deskripsi / Informasi</option>
                                        <option value="divider">Pemisah Bagian</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div>
                                <label class="block text-slate-700 text-sm font-bold mb-1.5">Order / Urutan</label>
                                <input type="number" wire:model="order" class="w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- Description & Placeholder -->
                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1.5">Keterangan / Deskripsi Bantuan (Opsional)</label>
                            <input type="text" wire:model="description" placeholder="Informasi pendukung agar user tidak bingung..." class="w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1.5">Placeholder Input (Opsional)</label>
                            <input type="text" wire:model="placeholder" placeholder="Teks bayangan di dalam kolom..." class="w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <!-- Checkbox: Required & Active -->
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_required" class="form-checkbox h-4.5 w-4.5 text-blue-600 border-slate-300 rounded">
                                <span class="ml-2 text-sm text-slate-700 font-bold">Wajib diisi</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_active" class="form-checkbox h-4.5 w-4.5 text-blue-600 border-slate-300 rounded">
                                <span class="ml-2 text-sm text-slate-700 font-bold">Aktif / Tampilkan</span>
                            </label>
                        </div>

                        <!-- Option Settings for Choice fields -->
                        @if(in_array($type, ['select', 'checkbox', 'radio', 'linear_scale']))
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3">
                                <div>
                                    <label class="block text-slate-700 text-xs font-bold mb-1">Opsi Jawaban (Pisahkan dengan koma)</label>
                                    <input type="text" wire:model="options" placeholder="Contoh: Pilihan A, Pilihan B, Pilihan C" class="w-full border-slate-300 rounded-lg shadow-sm py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white">
                                    <p class="text-[10px] text-slate-400 mt-1">Gunakan kata "Lainnya" di bagian akhir opsi jika ingin menyediakan isian bebas tambahan.</p>
                                </div>
                                <div class="border-t border-slate-200 pt-3">
                                    <label class="block text-slate-700 text-xs font-bold mb-1">Atau Import Cepat dari Baris Baru (Teks Multiline)</label>
                                    <textarea wire:model="importOptionsText" rows="3" placeholder="Pilihan A&#10;Pilihan B&#10;Pilihan C" class="w-full border-slate-300 rounded-lg shadow-sm py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-xs bg-white"></textarea>
                                    <button type="button" wire:click="importOptions" class="mt-1 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-1 px-2.5 rounded">Import Opsi</button>
                                    @if(session()->has('import_message'))
                                        <span class="text-xs text-green-600 font-bold ml-2">{{ session('import_message') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- File Upload Specific -->
                        @if($type === 'file')
                            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-slate-700 text-xs font-bold mb-1">Batas Jumlah File</label>
                                    <input type="number" wire:model="max_files" min="1" class="w-full border-slate-300 rounded-lg py-1.5 px-3 bg-white">
                                </div>
                                <div>
                                    <label class="block text-slate-700 text-xs font-bold mb-1">Ukuran Maksimal per File (MB)</label>
                                    <input type="number" wire:model="max_size_mb" min="1" class="w-full border-slate-300 rounded-lg py-1.5 px-3 bg-white">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-slate-700 text-xs font-bold mb-1">Ekstensi Diizinkan (Pisahkan dengan koma)</label>
                                    <input type="text" wire:model="allowed_types" placeholder="PDF, JPG, PNG, ZIP" class="w-full border-slate-300 rounded-lg py-1.5 px-3 bg-white">
                                </div>
                            </div>
                        @endif

                        <!-- Logic Conditional Visibility -->
                        <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 space-y-3">
                            <h4 class="text-xs font-bold text-indigo-900 flex items-center">
                                <svg class="w-4.5 h-4.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Logika Tampilan Kondisional (Conditional Logic)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-slate-700 text-[10px] font-bold mb-1">Jika Pertanyaan:</label>
                                    <select wire:model="trigger_field_id" class="w-full bg-white border-slate-300 rounded-lg text-xs py-1.5 px-2.5">
                                        <option value="">-- Tanpa Syarat --</option>
                                        @foreach($form->fields->where('id', '!=', $fieldId) as $f)
                                            <option value="{{ $f->id }}">{{ $f->label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-700 text-[10px] font-bold mb-1">Memiliki Kondisi:</label>
                                    <select wire:model="condition_operator" class="w-full bg-white border-slate-300 rounded-lg text-xs py-1.5 px-2.5">
                                        <option value="equals">Sama Dengan (equals)</option>
                                        <option value="not_equals">Tidak Sama Dengan (not equals)</option>
                                        <option value="contains">Mengandung Kata (contains)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-700 text-[10px] font-bold mb-1">Dengan Nilai Jawaban:</label>
                                    <input type="text" wire:model="condition_value" placeholder="Tuliskan nilainya..." class="w-full border-slate-300 rounded-lg text-xs py-1.5 px-2.5">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 sm:flex sm:flex-row-reverse rounded-b-xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto">
                            Simpan Pertanyaan
                        </button>
                        <button type="button" wire:click="closeFieldModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- SECTION EDITOR MODAL -->
    @if($isSectionModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-60" wire:click="closeSectionModal"></div>

        <div class="relative z-10 bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-lg">
                <form wire:submit.prevent="saveSection">
                    <div class="bg-white px-6 pt-6 pb-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-md font-bold text-slate-900">
                            {{ $sectionId ? 'Edit Section / Halaman' : 'Tambah Section Baru' }}
                        </h3>
                        <button type="button" wire:click="closeSectionModal" class="text-slate-400 hover:text-slate-600 font-bold">x</button>
                    </div>

                    <div class="bg-white px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1.5">Judul Section / Halaman</label>
                            <input type="text" wire:model="sectionTitle" placeholder="Contoh: Dokumen Pendukung..." class="w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @error('sectionTitle') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1.5">Deskripsi Singkat (Opsional)</label>
                            <input type="text" wire:model="sectionDescription" placeholder="Informasi singkat tentang isi halaman ini..." class="w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-slate-700 text-sm font-bold mb-1.5">Urutan Halaman</label>
                            <input type="number" wire:model="sectionOrder" class="w-full border-slate-300 rounded-lg shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 sm:flex sm:flex-row-reverse rounded-b-xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto">
                            Simpan Section
                        </button>
                        <button type="button" wire:click="closeSectionModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- PREVIEW DRAWER MODAL -->
    @if($isPreviewModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" wire:click="$set('isPreviewModalOpen', false)"></div>

        <!-- Responsive Box -->
        <div class="relative z-10 bg-slate-100 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full flex flex-col max-h-[90vh]
            {{ $previewMode === 'desktop' ? 'max-w-5xl' : ($previewMode === 'tablet' ? 'max-w-2xl' : 'max-w-sm') }}">
                
                <!-- Preview Toolbar -->
                <div class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 flex items-center">
                            <span class="h-2 w-2 bg-orange-500 rounded-full mr-2"></span>
                            Mode Preview: {{ ucfirst($previewMode) }}
                        </h3>
                        <span class="text-[10px] text-slate-400">Data preview tidak akan masuk ke dalam database</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Mode Toggles -->
                        <button type="button" wire:click="$set('previewMode', 'desktop')" class="p-1.5 rounded-lg {{ $previewMode === 'desktop' ? 'bg-slate-100 text-blue-600' : 'text-slate-400 hover:bg-slate-50' }}">
                            Desktop
                        </button>
                        <button type="button" wire:click="$set('previewMode', 'tablet')" class="p-1.5 rounded-lg {{ $previewMode === 'tablet' ? 'bg-slate-100 text-blue-600' : 'text-slate-400 hover:bg-slate-50' }}">
                            Tablet
                        </button>
                        <button type="button" wire:click="$set('previewMode', 'mobile')" class="p-1.5 rounded-lg {{ $previewMode === 'mobile' ? 'bg-slate-100 text-blue-600' : 'text-slate-400 hover:bg-slate-50' }}">
                            Mobile
                        </button>
                        <button type="button" wire:click="$set('isPreviewModalOpen', false)" class="ml-4 text-slate-500 hover:text-slate-700 font-bold">x</button>
                    </div>
                </div>

                <!-- Preview Container -->
                <div class="p-6 max-h-[80vh] overflow-y-auto space-y-6">
                    
                    <!-- Student Identity Card Simulation -->
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Identitas Mahasiswa Pengisi (Otomatis)</h4>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-500">NIM</span>
                                <p class="font-semibold text-slate-800">20230101999 (Sample)</p>
                            </div>
                            <div>
                                <span class="text-slate-500">Nama Lengkap</span>
                                <p class="font-semibold text-slate-800">Budi Mahasiswa (Sample)</p>
                            </div>
                            <div>
                                <span class="text-slate-500">Email Pradita</span>
                                <p class="font-semibold text-slate-800">budi.mahasiswa@student.pradita.ac.id</p>
                            </div>
                            <div>
                                <span class="text-slate-500">Angkatan</span>
                                <p class="font-semibold text-slate-800">2023</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Render of Single Page Form -->
                    @php
                        $allFields = collect();
                        $globalFields = $form->fields->whereNull('section_id')->sortBy('order');
                        if($globalFields->count() > 0) {
                            $allFields->push(['type' => 'header', 'title' => 'Bagian Utama']);
                            foreach($globalFields as $f) { $allFields->push($f); }
                        }

                        foreach($form->sections->sortBy('order') as $sec) {
                            $secFields = $form->fields->where('section_id', $sec->id)->sortBy('order');
                            if($secFields->count() > 0) {
                                $allFields->push(['type' => 'header', 'title' => $sec->title, 'desc' => $sec->description]);
                                foreach($secFields as $f) { $allFields->push($f); }
                            }
                        }
                    @endphp

                    <!-- Render Questions -->
                    <div class="space-y-4">
                        @forelse($allFields as $field)
                            @if(is_array($field) && $field['type'] === 'header')
                                <div class="border-b border-slate-200 pb-2 mt-8 mb-4">
                                    <h3 class="text-xl font-bold text-slate-800">{{ $field['title'] }}</h3>
                                    @if(isset($field['desc']) && $field['desc'])
                                        <p class="text-sm text-slate-500 mt-1">{{ $field['desc'] }}</p>
                                    @endif
                                </div>
                            @else
                                @if($field->is_active)
                                    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-2">
                                        
                                        <!-- Label & Required -->
                                        <label class="block text-sm font-bold text-slate-800">
                                            {{ $field->label }}
                                            @if($field->is_required)
                                                <span class="text-red-500 ml-0.5">*</span>
                                            @endif
                                        </label>

                                        <!-- Description -->
                                        @if($field->description)
                                            <p class="text-xs text-slate-500">{{ $field->description }}</p>
                                        @endif

                                        <!-- Render inputs dynamically based on type -->
                                        @if($field->type === 'text')
                                            <input type="text" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'textarea')
                                            <textarea rows="3" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                                        @elseif($field->type === 'email')
                                            <input type="email" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'tel')
                                            <input type="tel" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'number')
                                            <input type="number" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'url')
                                            <input type="url" placeholder="{{ $field->placeholder }}" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'date')
                                            <input type="date" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'time')
                                            <input type="time" class="w-full border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                        @elseif($field->type === 'date_range')
                                            <div class="grid grid-cols-2 gap-3">
                                                <input type="date" class="border-slate-300 rounded-lg py-2 px-3 text-sm">
                                                <input type="date" class="border-slate-300 rounded-lg py-2 px-3 text-sm">
                                            </div>
                                        @elseif($field->type === 'select' || $field->type === 'dropdown')
                                            <select class="w-full bg-white border-slate-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">-- Pilih --</option>
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $opt)
                                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        @elseif($field->type === 'radio')
                                            <div class="space-y-1.5">
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $opt)
                                                        <label class="flex items-center text-sm">
                                                            <input type="radio" name="preview_radio_{{ $field->id }}" value="{{ $opt }}" class="h-4 w-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                                                            <span class="ml-2 text-slate-700">{{ $opt }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field->type === 'checkbox')
                                            <div class="space-y-1.5">
                                                @if(is_array($field->options))
                                                    @foreach($field->options as $opt)
                                                        <label class="flex items-center text-sm">
                                                            <input type="checkbox" value="{{ $opt }}" class="h-4.5 w-4.5 text-blue-600 border-slate-300 rounded">
                                                            <span class="ml-2 text-slate-700">{{ $opt }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @elseif($field->type === 'linear_scale')
                                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                                <span class="text-xs text-slate-500">Min</span>
                                                <div class="flex items-center space-x-4">
                                                    @for($i=1; $i<=5; $i++)
                                                        <label class="flex flex-col items-center">
                                                            <input type="radio" name="preview_linear_{{ $field->id }}" class="text-blue-600 focus:ring-blue-500">
                                                            <span class="text-xs text-slate-600 mt-1 font-bold">{{ $i }}</span>
                                                        </label>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-slate-500">Max</span>
                                            </div>
                                        @elseif($field->type === 'file')
                                            <div class="flex flex-col items-center justify-center border-2 border-dashed border-slate-300 rounded-lg p-4 bg-slate-50">
                                                <p class="text-xs text-slate-500">Unggah File (Simulasi)</p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">Maks: {{ $field->max_files }} file ({{ $field->max_size_mb }}MB) / {{ is_array($field->allowed_types) ? implode(', ', $field->allowed_types) : 'Bebas' }}</p>
                                            </div>
                                        @elseif($field->type === 'section_title')
                                            <div class="border-b border-slate-100 pb-2">
                                                <h4 class="text-sm font-bold text-slate-900">{{ $field->label }}</h4>
                                            </div>
                                        @elseif($field->type === 'info')
                                            <div class="bg-slate-50 rounded-lg p-3 text-xs text-slate-600 border border-slate-150">
                                                {{ $field->label }}
                                            </div>
                                        @elseif($field->type === 'divider')
                                            <hr class="border-slate-200 my-2">
                                        @endif

                                    </div>
                                @endif
                            @endif
                        @empty
                            <p class="text-center text-slate-400 text-xs py-4">Belum ada pertanyaan.</p>
                        @endforelse
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-between items-center bg-white border-t border-slate-200 px-6 py-4 rounded-xl mt-4">
                        <div class="w-full text-right">
                            <button type="button" class="bg-emerald-600 text-white font-semibold py-2 px-6 rounded-lg text-sm disabled:opacity-70 cursor-not-allowed">
                                Submit (Simulasi)
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- QUESTION BANK OVERLAY MODAL -->
    @if($isBankModalOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
        <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-60" wire:click="closeBankModal"></div>

        <div class="relative z-10 bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-3xl">
                <div class="bg-white px-6 pt-6 pb-4 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-md font-bold text-slate-900">Bank Pertanyaan Reusable</h3>
                        <p class="text-xs text-slate-500">Pilih dari daftar pertanyaan umum untuk langsung ditambahkan ke form.</p>
                    </div>
                    <button type="button" wire:click="closeBankModal" class="text-slate-400 hover:text-slate-600 font-bold">x</button>
                </div>

                <div class="bg-slate-50 px-6 py-3 border-b border-slate-100 flex gap-4">
                    <!-- Search -->
                    <input type="text" wire:model.live="bankSearch" placeholder="Cari pertanyaan..." class="w-full max-w-xs border-slate-300 rounded-lg text-xs py-1.5 px-3">
                    
                    <!-- Filter Category -->
                    <select wire:model.live="bankCategory" class="w-full max-w-xs bg-white border-slate-300 rounded-lg text-xs py-1.5 px-3">
                        <option value="">-- Semua Kategori --</option>
                        <option value="Identitas">Identitas</option>
                        <option value="Kegiatan">Kegiatan</option>
                        <option value="Tanggal">Tanggal</option>
                        <option value="Instansi">Instansi</option>
                        <option value="Akademik">Akademik</option>
                        <option value="Dokumen">Dokumen</option>
                        <option value="Evaluasi">Evaluasi</option>
                        <option value="Pelaporan">Pelaporan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="bg-white px-6 py-4 space-y-3 max-h-[50vh] overflow-y-auto">
                    @forelse($bankQuestions as $bq)
                        <div class="flex justify-between items-start p-3 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                            <div>
                                <span class="bg-indigo-50 text-indigo-800 text-[10px] font-semibold px-2 py-0.5 rounded">{{ $bq->category }}</span>
                                <h4 class="text-xs font-bold text-slate-800 mt-1">{{ $bq->label }}</h4>
                                <span class="text-[10px] text-slate-400">Tipe: {{ $bq->type }}</span>
                            </div>
                            <button type="button" wire:click="addFromBank({{ $bq->id }})" class="bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold py-1 px-3 rounded-lg transition-colors">
                                + Tambahkan
                            </button>
                        </div>
                    @empty
                        <p class="text-center text-slate-400 text-xs py-6 italic">Tidak ada pertanyaan yang sesuai di bank.</p>
                    @endforelse
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-right">
                    <button type="button" wire:click="closeBankModal" class="bg-white hover:bg-slate-50 text-slate-700 font-semibold py-2 px-4 rounded-lg text-sm border border-slate-300 transition-colors">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formBuilder', () => ({
                init() {
                    // Sortable for Sections
                    const sectionsEl = document.getElementById('sections-list');
                    if (sectionsEl) {
                        new Sortable(sectionsEl, {
                            animation: 150,
                            handle: '.section-drag-handle',
                            onEnd: (evt) => {
                                const orderedIds = Array.from(sectionsEl.children).map(child => child.dataset.id);
                                @this.updateSectionOrder(orderedIds);
                            }
                        });
                    }
                },
                initFieldSortable(el, sectionId) {
                    new Sortable(el, {
                        animation: 150,
                        group: 'fields', // Allow dragging between sections
                        handle: '.field-drag-handle',
                        onEnd: (evt) => {
                            const newSectionId = evt.to.dataset.sectionId;
                            const orderedIds = Array.from(evt.to.children).map(child => child.dataset.id);
                            
                            @this.updateFieldOrder(newSectionId, orderedIds);
                        }
                    });
                }
            }));
        });
    </script>
</div>
