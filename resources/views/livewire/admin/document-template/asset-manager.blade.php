<div>
    @section('title', 'Manajemen Aset')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Manajemen Aset Dokumen</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola logo, stempel, dan gambar untuk digunakan pada Document Builder.</p>
        </div>
        <button wire:click="$set('showUploadForm', true)" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            + Upload Aset
        </button>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded"><p class="text-sm text-green-700">{{ session('message') }}</p></div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded"><p class="text-sm text-red-700">{{ session('error') }}</p></div>
    @endif

    {{-- Filters --}}
    <div class="bg-white shadow rounded-lg p-4 mb-4 flex gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama aset..." class="rounded-md border-gray-300 shadow-sm text-sm flex-1">
        <select wire:model.live="typeFilter" class="rounded-md border-gray-300 shadow-sm text-sm w-48">
            <option value="">Semua Tipe</option>
            <option value="logo">Logo</option>
            <option value="stamp">Stempel</option>
            <option value="image">Gambar Umum</option>
            <option value="photo">Foto</option>
        </select>
    </div>

    {{-- Assets Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse($assets as $asset)
            <div class="bg-white border rounded-lg shadow-sm overflow-hidden flex flex-col">
                <div class="h-40 bg-gray-50 flex items-center justify-center p-4 border-b checkerboard">
                    @if($asset->activeVersion)
                        <img src="{{ asset('storage/' . $asset->activeVersion->original_path) }}" 
                             alt="{{ $asset->name }}" 
                             class="max-w-full max-h-full"
                             style="transform: rotate({{ $asset->activeVersion->rotation ?? 0 }}deg); object-fit: {{ $asset->activeVersion->object_fit ?? 'contain' }}; width: {{ $asset->default_width }}px; height: {{ $asset->default_height }}px;">
                    @else
                        <span class="text-gray-400">Preview tidak tersedia</span>
                    @endif
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="font-medium text-gray-900 truncate" title="{{ $asset->name }}">{{ $asset->name }}</h3>
                    <div class="text-xs text-gray-500 mt-1 flex justify-between">
                        <span class="uppercase font-semibold text-gray-600">{{ $asset->asset_type }}</span>
                        <span>{{ $asset->default_width }}x{{ $asset->default_height }} px</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        Diunggah: {{ $asset->created_at->format('d M Y') }}
                    </div>
                    <div class="mt-4 flex gap-2 pt-2 border-t mt-auto">
                        <button wire:click="editAsset({{ $asset->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Edit Ukuran</button>
                        <button wire:click="deleteAsset({{ $asset->id }})" wire:confirm="Yakin hapus aset ini?" class="text-red-600 hover:text-red-800 text-sm font-medium ml-auto">Hapus</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg p-12 text-center text-gray-500 border">
                Belum ada aset. Klik tombol upload untuk menambahkan.
            </div>
        @endforelse
    </div>

    {{-- Upload Modal --}}
    @if($showUploadForm)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
            <h3 class="text-lg font-semibold mb-4">Upload Aset Baru</h3>
            <form wire:submit.prevent="uploadAsset" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Aset *</label>
                    <input type="text" wire:model="newName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipe Aset *</label>
                    <select wire:model="newType" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm" required>
                        <option value="logo">Logo</option>
                        <option value="stamp">Stempel</option>
                        <option value="image">Gambar Umum</option>
                        <option value="photo">Foto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">File Gambar *</label>
                    <input type="file" wire:model="newAsset" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="mt-1 block w-full text-sm" required>
                    <div wire:loading wire:target="newAsset" class="text-xs text-blue-600 mt-1">Membaca file...</div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" wire:click="$set('showUploadForm', false)" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700" wire:loading.attr="disabled">Upload</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Edit Image Size Modal --}}
    @if($showEditorModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6" x-data="imageEditorData()">
            <h3 class="text-lg font-semibold mb-4">Edit Ukuran Proporsional</h3>
            
            @php $assetToEdit = $assets->firstWhere('id', $editingAssetId); @endphp
            
            <div class="mb-4">
                <div class="h-48 bg-gray-100 flex items-center justify-center border checkerboard overflow-hidden relative">
                    <img src="{{ asset('storage/' . $assetToEdit->activeVersion->original_path) }}" 
                         :style="`width: ${width}px; height: ${height}px; transform: rotate(${rotation}deg); object-fit: ${objectFit};`"
                         class="transition-all duration-200 shadow-sm border border-dashed border-blue-400">
                </div>
                <p class="text-xs text-center text-gray-500 mt-2">Preview (batas luar mewakili bounding box)</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Width (px)</label>
                    <input type="number" x-model="width" @input="updateProportional('width')" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Height (px)</label>
                    <input type="number" x-model="height" @input="updateProportional('height')" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Rotasi (derajat)</label>
                    <input type="number" x-model="rotation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Object Fit</label>
                    <select x-model="objectFit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="contain">Contain (Proporsional Utuh)</option>
                        <option value="cover">Cover (Penuhi Kotak)</option>
                        <option value="fill">Fill (Abaikan Rasio)</option>
                    </select>
                </div>
            </div>
            
            <div class="flex items-center gap-2 mb-4">
                <input type="checkbox" x-model="lockRatio" id="lockRatio" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="lockRatio" class="text-sm text-gray-700 cursor-pointer">Lock Aspect Ratio (Pertahankan Proporsi)</label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" wire:click="$set('showEditorModal', false)" class="px-4 py-2 text-sm border rounded hover:bg-gray-50">Batal</button>
                <button type="button" @click="saveToServer" class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </div>
    </div>
    @endif

    <style>
        .checkerboard {
            background-color: #f8f9fa;
            background-image: linear-gradient(45deg, #e9ecef 25%, transparent 25%, transparent 75%, #e9ecef 75%, #e9ecef), 
                              linear-gradient(45deg, #e9ecef 25%, transparent 25%, transparent 75%, #e9ecef 75%, #e9ecef);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('imageEditorData', () => ({
                width: @entangle('editorData.width'),
                height: @entangle('editorData.height'),
                rotation: @entangle('editorData.rotation'),
                objectFit: @entangle('editorData.object_fit'),
                lockRatio: true,
                originalRatio: 1,

                init() {
                    // Set ratio immediately
                    if (this.height > 0) {
                        this.originalRatio = this.width / this.height;
                    }
                },

                updateProportional(changedField) {
                    if (!this.lockRatio) return;
                    
                    if (changedField === 'width') {
                        this.height = Math.round(this.width / this.originalRatio);
                    } else if (changedField === 'height') {
                        this.width = Math.round(this.height * this.originalRatio);
                    }
                },

                saveToServer() {
                    // Trigger livewire save
                    @this.saveEdits();
                }
            }));
        });
    </script>
    @endpush
</div>
