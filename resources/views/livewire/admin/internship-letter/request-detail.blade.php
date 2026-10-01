<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Permohonan Surat Magang</h2>
            <p class="text-sm text-gray-600 mt-1">Review data pengajuan dan generate PDF resmi.</p>
        </div>
        <a href="{{ route('admin.internship-letters.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Kembali ke Daftar
        </a>
    </div>

    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Detail Pengajuan -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Pemohon</h3>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">
                        Status: {{ strtoupper($request->status) }}
                    </span>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->student->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIM</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->student->nim }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Periode Magang</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $request->start_date->format('d M Y') }} s/d 
                                {{ $request->end_date ? $request->end_date->format('d M Y') : '(-)' }}
                                @if($request->duration_notes) ({{ $request->duration_notes }}) @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Posisi/Divisi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->internship_position ?? '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Tujuan Magang</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->purpose ?? 'Standar (Memohon izin kerja praktik)' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Form Koreksi Perusahaan (Hanya jika belum generated) -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Koreksi Data Permohonan</h3>
                    <p class="mt-1 text-xs text-gray-500">Admin dapat memperbaiki typo, mengatur ulang tanggal, atau menginput nomor surat (jika sudah ada sebelumnya).</p>
                </div>
                <div class="p-6">
                    <form wire:submit.prevent="updateCompanyDetails" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Nomor Surat (Otomatis jika kosong)</label>
                                <input type="text" wire:model="letter_number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: 001/P-KP/Pradita/IX/2026" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semester Saat Pengajuan</label>
                                <input type="text" wire:model="semester" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Gasal 2026/2027" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Terbit Surat</label>
                                <input type="date" wire:model="letter_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai Magang</label>
                                <input type="date" wire:model="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai Magang</label>
                                <input type="date" wire:model="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                                <input type="text" wire:model="company_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penerima/Jabatan</label>
                            <input type="text" wire:model="recipient_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat Perusahaan</label>
                            <textarea wire:model="company_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif></textarea>
                        </div>
                        @if(!in_array($request->status, ['generated', 'completed']))
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Simpan Perubahan
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Aksi & Riwayat -->
        <div class="space-y-6">
            
            <!-- Aksi Admin -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Aksi</h3>
                </div>
                <div class="p-6 space-y-6">
                    
                    @if(in_array($request->status, ['submitted', 'under_review', 'revision_required', 'approved']))
                    <!-- Setujui & Generate -->
                    <div>
                        <button wire:click="approveAndGenerate" wire:loading.attr="disabled" class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                            <span wire:loading.remove wire:target="approveAndGenerate">Buat Draft PDF & Atur TTD</span>
                            <span wire:loading wire:target="approveAndGenerate">Memproses PDF...</span>
                        </button>
                        <p class="text-xs text-gray-500 mt-2">Menyetujui akan langsung mencetak draft PDF, menyimpan nomor surat, lalu membuka form penempatan stempel tanda tangan.</p>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Minta Revisi -->
                    <form wire:submit.prevent="askRevision">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Revisi</label>
                        <textarea wire:model="revision_note" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-2" placeholder="Jelaskan apa yang perlu diperbaiki..."></textarea>
                        @error('revision_note') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500">
                            Kembalikan untuk Revisi
                        </button>
                    </form>

                    <!-- Tolak -->
                    <form wire:submit.prevent="reject">
                        <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Alasan Penolakan</label>
                        <textarea wire:model="rejection_reason" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm mb-2" placeholder="Jelaskan mengapa ditolak..."></textarea>
                        @error('rejection_reason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                            Tolak Permanen
                        </button>
                    </form>
                    @endif

                    @if(in_array($request->status, ['generated', 'completed']))
                    <div>
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm font-medium text-green-800">Surat telah di-generate.</p>
                            <p class="text-xs text-green-700 mt-1">No: {{ $request->letter_number }}</p>
                        </div>
                        <button wire:click="downloadPdf" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Download PDF Final
                        </button>

                        <button wire:click="resendEmail" wire:loading.attr="disabled" class="w-full mt-3 inline-flex justify-center items-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                            <span wire:loading.remove wire:target="resendEmail">Kirim Notifikasi Email</span>
                            <span wire:loading wire:target="resendEmail">Mengirim Email...</span>
                        </button>
                        
                        <div class="mt-4 border-t pt-4">
                            <button wire:click="approveAndGenerate" onclick="confirm('Yakin ingin merender ulang PDF? Nomor surat akan tetap sama. Anda harus mengatur ulang posisi tanda tangan.') || event.stopImmediatePropagation()" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Render Ulang & Atur TTD
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            <!-- Audit Trail -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Riwayat Audit</h3>
                </div>
                <div class="p-6">
                    <ul role="list" class="space-y-4">
                        @foreach($request->histories()->orderBy('created_at', 'desc')->get() as $history)
                        <li>
                            <div class="flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-medium text-gray-900">{{ $history->actor->name ?? 'Sistem' }}</span> 
                                            {{ str_replace('_', ' ', $history->action) }}
                                        </p>
                                        @if($history->note)
                                        <p class="text-xs text-gray-600 mt-1 italic">"{{ $history->note }}"</p>
                                        @endif
                                    </div>
                                    <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                        {{ $history->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- Preview & Sign Modal -->
    @if($previewRequestId)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-5xl bg-white shadow-xl rounded-lg flex flex-col" style="max-height: 90vh;">
            <div class="p-4 border-b bg-gray-50 rounded-t-lg flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Review Draft & Atur Posisi Tanda Tangan</h3>
                <button wire:click="closePreviewModal" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
            </div>
            
            <div class="p-4 flex-1 overflow-y-auto">
                <!-- Lecturer Selector -->
                <div class="mb-6 p-4 border border-blue-200 bg-blue-50 rounded-lg">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-blue-800 text-sm font-bold">Penandatangan <span class="text-red-500">*</span></label>
                        <button wire:click="addPlacement" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-bold shadow"><i class="fas fa-plus mr-1"></i> Tambah TTD (Hal. ini)</button>
                    </div>
                    
                    @if(count($availableLecturers) > 0)
                        @foreach($placements as $index => $placement)
                            <div class="bg-white p-3 rounded shadow-sm mb-3 border border-gray-200 relative">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold text-gray-700 text-sm">Tanda Tangan #{{ $index + 1 }} (Halaman {{ $placement['page'] }})</span>
                                    @if(count($placements) > 1)
                                        <button wire:click="removePlacement({{ $index }})" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i> Hapus</button>
                                    @endif
                                </div>
                                <select wire:model.live="placements.{{ $index }}.lecturer_id" class="shadow border border-blue-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 mb-2">
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @foreach($availableLecturers as $lec)
                                        <option value="{{ $lec->id }}">{{ $lec->name }} - {{ $lec->position }}</option>
                                    @endforeach
                                </select>
                                <label class="inline-flex items-center mt-2 cursor-pointer">
                                    <input type="checkbox" wire:model.live="placements.{{ $index }}.include_stamp" class="form-checkbox h-4 w-4 text-blue-600 rounded">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">Sertakan Stempel Jabatan</span>
                                </label>
                            </div>
                        @endforeach
                        @if(empty($placements[0]['lecturer_id']))
                            <p class="text-red-500 text-xs mt-2 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Anda tidak bisa menyetujui pengajuan sebelum memilih penandatangan.</p>
                        @endif
                    @else
                        <div class="p-3 bg-red-100 text-red-800 rounded border border-red-300">
                            <strong>Peringatan!</strong> Belum ada penandatangan aktif yang dapat Anda gunakan. Tambahkan atau aktifkan penandatangan melalui pengaturan Penandatangan Surat.
                        </div>
                    @endif
                </div>
                
                <!-- PDF Viewer Controls -->
                <div class="flex flex-wrap items-center justify-between mb-2 p-2 bg-gray-800 text-white rounded-t shadow-sm w-full">
                    <div class="flex items-center space-x-2">
                        <button type="button" id="prevPage" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm disabled:opacity-50" disabled>&#8592; Prev</button>
                        <span class="text-sm font-bold">Halaman <span id="pageNum">1</span> dari <span id="pageCount">?</span></span>
                        <button type="button" id="nextPage" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm disabled:opacity-50" disabled>Next &#8594;</button>
                    </div>
                    <div class="flex items-center space-x-2 mt-2 sm:mt-0">
                        <button type="button" id="zoomOut" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm"><i class="fas fa-search-minus"></i></button>
                        <span class="text-sm font-bold" id="zoomLevel">100%</span>
                        <button type="button" id="zoomIn" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm"><i class="fas fa-search-plus"></i></button>
                        <button type="button" id="fitWidth" class="bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded text-sm"><i class="fas fa-expand-arrows-alt"></i> Fit</button>
                    </div>
                </div>

                <!-- PDF Viewer Container -->
                <div id="pdfContainerWrapper" class="relative bg-gray-200 border border-gray-400 p-4 rounded-b overflow-auto flex justify-center" style="max-height: 500px;">
                    <div id="pdfError" class="hidden absolute inset-0 bg-white/90 flex flex-col items-center justify-center z-50">
                        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-2"></i>
                        <p class="text-gray-800 font-bold" id="pdfErrorMessage">Gagal memuat PDF.</p>
                    </div>
                    
                    <div id="pdfStage" wire:ignore.self class="relative inline-block shadow-lg bg-white" style="touch-action: none;">
                        <canvas id="pdfCanvas" wire:ignore class="block"></canvas>
                        
                        <!-- Draggable Boxes -->
                        @foreach($placements as $index => $placement)
                            <div id="signatureBox-{{ $placement['id'] ?? $index }}" wire:key="box-{{ $placement['id'] ?? $index }}" data-index="{{ $index }}" data-page="{{ $placement['page'] }}" 
                                 data-init-x="{{ $placement['x'] }}" data-init-y="{{ $placement['y'] }}" data-init-w="{{ $placement['width'] }}" data-init-h="{{ $placement['height'] }}"
                                 class="signature-box absolute top-0 left-0 border-2 border-dashed border-blue-500 bg-blue-100 bg-opacity-50 flex flex-col items-center justify-center overflow-hidden" 
                                 style="display: none; touch-action: none;">
                                
                                <div class="absolute top-0 left-0 bg-blue-500 text-white text-[10px] px-1 rounded-br opacity-70 select-none z-20">#{{ $index + 1 }}</div>
                                
                                @if(!empty($placement['include_stamp']))
                                    @if(!empty($placement['stamp_url']))
                                        <img src="{{ $placement['stamp_url'] }}" alt="Stamp Preview" class="w-full h-full object-contain select-none pointer-events-none opacity-90 z-10" />
                                    @else
                                        <!-- Placeholder fallback if no stamp uploaded -->
                                        <div class="relative w-full h-full flex items-center justify-center">
                                            <div class="absolute w-full h-full bg-red-100 opacity-40 border border-red-300 rounded-full flex items-center justify-center text-red-500 text-xs font-bold -rotate-12 z-0">STEMPEL</div>
                                            @if(!empty($placement['signature_url']))
                                                <img src="{{ $placement['signature_url'] }}" alt="Signature Preview" class="absolute w-3/4 h-3/4 object-contain select-none pointer-events-none opacity-90 z-10" />
                                            @endif
                                        </div>
                                    @endif
                                @elseif(!empty($placement['signature_url']))
                                    <img src="{{ $placement['signature_url'] }}" alt="Signature Preview" class="w-full h-full object-contain select-none pointer-events-none opacity-90 z-10" />
                                @else
                                    <div class="text-[10px] font-bold text-blue-800 text-center select-none pointer-events-none px-1 z-10">
                                        TTD #{{ $index + 1 }}
                                    </div>
                                @endif
                                
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Form Debug Inputs (Hidden but synced) -->
                <div class="hidden">
                    <input type="number" wire:model.live="previewPage" id="wire_previewPage">
                </div>
            </div>
            
            <div class="p-4 border-t bg-gray-50 rounded-b-lg flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                <div class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                    Bisa di-drag dan di-resize dari semua sisi sudut menggunakan interact.js
                </div>
                <div class="flex space-x-3">
                    <button wire:click="closePreviewModal" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button wire:click="finalizeSignature" wire:loading.attr="disabled" class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                        Setujui & Tanda Tangani
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @push('scripts')
    @vite(['resources/js/signature-editor.js'])
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updating', ({ el, toEl, skip, childrenOnly }) => {
                if (el.classList && el.classList.contains('signature-box')) {
                    // Preserve the inline styles (transform, width, height, etc) added by JS
                    if (el.hasAttribute('style')) {
                        toEl.setAttribute('style', el.getAttribute('style'));
                    }
                    if (el.hasAttribute('data-x')) {
                        toEl.setAttribute('data-x', el.getAttribute('data-x'));
                    }
                    if (el.hasAttribute('data-y')) {
                        toEl.setAttribute('data-y', el.getAttribute('data-y'));
                    }
                }
            });

            Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                succeed(({ snapshot, effect }) => {
                    setTimeout(() => {
                        if (typeof window.refreshSignatureBoxes === 'function') {
                            window.refreshSignatureBoxes();
                        }
                    }, 50);
                });
            });
        });
    </script>
    @endpush
</div>
