<div>
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Daftar Pengajuan Tanda Tangan</h2>
    </div>

    <div class="mb-4 flex space-x-4">
        <input type="text" wire:model.live="search" placeholder="Cari nama mahasiswa atau judul..." class="shadow border rounded w-1/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        <select wire:model.live="statusFilter" class="shadow border rounded w-1/4 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Semua Status</option>
            <option value="submitted">Menunggu Review</option>
            <option value="signing">Sedang Ditandatangani</option>
            <option value="completed">Selesai / Email Terkirim</option>
            <option value="rejected">Ditolak</option>
        </select>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Waktu</th>
                    <th class="py-2 px-4 border-b">Mahasiswa</th>
                    <th class="py-2 px-4 border-b">Judul Dokumen</th>
                    <th class="py-2 px-4 border-b">Penandatangan</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr class="hover:bg-gray-100 border-b">
                    <td class="py-2 px-4">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2 px-4">{{ $request->student->name ?? 'Unknown' }}</td>
                    <td class="py-2 px-4">{{ $request->title }}</td>
                    <td class="py-2 px-4">{{ $request->lecturer->name ?? '-' }}</td>
                    <td class="py-2 px-4">
                        @if($request->status == 'completed')
                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">Selesai</span>
                        @elseif($request->status == 'rejected')
                            <span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs">Ditolak</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center space-x-2">
                        <a href="{{ route('admin.file.download', ['path' => $request->original_file_path]) }}" target="_blank" class="text-blue-500 hover:text-blue-700 text-sm">Lihat Asli</a>
                        
                        @if($request->status == 'submitted')
                            <button wire:click="openPreviewModal({{ $request->id }})" class="text-green-600 hover:text-green-800 font-semibold text-sm">Preview & Sign</button>
                            <button wire:click="reject({{ $request->id }})" class="text-yellow-600 hover:text-yellow-800 text-sm" onclick="confirm('Tolak pengajuan ini?') || event.stopImmediatePropagation()">Tolak</button>
                        @elseif(in_array($request->status, ['completed', 'approved', 'signed', 'email_failed']))
                            @if($request->signed_file_path)
                            <a href="{{ route('admin.file.download', ['path' => $request->signed_file_path]) }}" target="_blank" class="text-green-600 hover:text-green-800 text-sm font-semibold">Lihat Hasil</a>
                            @endif
                            
                            @if(in_array($request->status, ['approved', 'signed', 'email_failed']))
                                <button wire:click="sendEmailAction({{ $request->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-semibold ml-2">Kirim Email</button>
                            @endif
                        @endif
                        <button wire:click="delete({{ $request->id }})" class="text-red-500 hover:text-red-700 text-sm font-semibold ml-2" onclick="confirm('Hapus permanen pengajuan ini?') || event.stopImmediatePropagation()">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-4 text-center text-gray-500">Tidak ada data pengajuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Preview & Sign Modal -->
    @if($previewRequestId)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative w-full max-w-5xl bg-white shadow-xl rounded-lg flex flex-col" style="max-height: 90vh;">
            <div class="p-4 border-b bg-gray-50 rounded-t-lg flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-800">Review & Atur Posisi Tanda Tangan</h3>
                <button wire:click="closePreviewModal" class="text-gray-500 hover:text-gray-700 text-2xl font-bold">&times;</button>
            </div>
            
            <div class="p-4 flex-1 overflow-y-auto">
                <div class="mb-4">
                    <p class="text-sm font-semibold text-gray-600 mb-1">Dokumen:</p>
                    <p class="text-md text-gray-900 font-bold">{{ $previewRequestTitle }}</p>
                    <a href="{{ route('admin.file.download', ['path' => $previewRequestOriginalPath]) }}" target="_blank" class="inline-block mt-1 text-sm text-blue-600 hover:underline">Buka PDF Asli di Tab Baru <i class="fas fa-external-link-alt ml-1"></i></a>
                </div>

                <!-- Lecturer Selector -->
                <div class="mb-6 p-4 border border-blue-200 bg-blue-50 rounded-lg">
                    <label class="block text-blue-800 text-sm font-bold mb-2">Pilih Penandatangan <span class="text-red-500">*</span></label>
                    
                    @if(count($availableLecturers) > 0)
                        <select wire:model.live="selectedLecturerId" class="shadow border border-blue-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Wajib Pilih Penandatangan --</option>
                            @foreach($availableLecturers as $lec)
                                <option value="{{ $lec->id }}">{{ $lec->name }} - {{ $lec->position }}</option>
                            @endforeach
                        </select>
                        @if(!$selectedLecturerId)
                            <p class="text-red-500 text-xs mt-2 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Anda tidak bisa menyetujui pengajuan sebelum memilih penandatangan.</p>
                        @endif
                    @else
                        <div class="p-3 bg-red-100 text-red-800 rounded border border-red-300">
                            <strong>Peringatan!</strong> Belum ada penandatangan aktif yang dapat Anda gunakan. Tambahkan atau aktifkan penandatangan melalui 
                            <a href="{{ route('admin.signatories.index') }}" class="font-bold underline text-blue-600 hover:text-blue-800">Pengaturan Penandatangan Surat</a>.
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
                    
                    <div id="pdfStage" wire:ignore class="relative inline-block shadow-lg bg-white" style="touch-action: none;">
                        <canvas id="pdfCanvas" class="block"></canvas>
                        
                        <!-- Draggable Box -->
                        <div id="signatureBox" class="absolute border-2 border-dashed border-blue-500 bg-blue-100 bg-opacity-50 flex items-center justify-center overflow-hidden" 
                             style="width: 150px; height: 75px; top: 10px; left: 10px; display: none; touch-action: none;">
                            
                            @if($selectedLecturerSignatureUrl)
                                <img src="{{ $selectedLecturerSignatureUrl }}" alt="Signature Preview" class="w-full h-full object-contain select-none pointer-events-none opacity-80" />
                            @else
                                <div class="text-xs font-bold text-blue-800 text-center select-none pointer-events-none px-1">
                                    Pilih Penandatangan<br>Terlebih Dahulu
                                </div>
                            @endif
                            
                        </div>
                    </div>
                </div>

                <!-- Form Debug Inputs (Hidden but synced) -->
                <div class="hidden">
                    <input type="number" wire:model="previewPage" id="wire_previewPage">
                    <input type="number" wire:model="previewX" id="wire_previewX" step="0.1">
                    <input type="number" wire:model="previewY" id="wire_previewY" step="0.1">
                    <input type="number" wire:model="previewWidth" id="wire_previewWidth" step="0.1">
                    <input type="number" wire:model="previewHeight" id="wire_previewHeight" step="0.1">
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
                    <button wire:click="approveAndSign" wire:loading.attr="disabled" @if(!$selectedLecturerId) disabled @endif class="px-4 py-2 bg-green-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">
                        Setujui & Tanda Tangani
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    @push('scripts')
    @vite(['resources/js/signature-editor.js'])
    @endpush
</div>
