<div>
    @section('title', 'Template Surat')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Template Surat</h2>
            <p class="text-sm text-gray-500 mt-1">Buat, kelola, dan atur template dokumen untuk seluruh kebutuhan FORTA.</p>
        </div>
        <a href="{{ route('admin.document-templates.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium shadow-sm">
            + Buat Template Baru
        </a>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded"><p class="text-sm text-green-700">{{ session('message') }}</p></div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded"><p class="text-sm text-red-700">{{ session('error') }}</p></div>
    @endif

    {{-- Filters --}}
    <div class="bg-white shadow rounded-lg p-4 mb-4 flex flex-wrap gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama / kode / peruntukan..."
               class="rounded-md border-gray-300 shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500 flex-1 min-w-[200px]">
        <select wire:model.live="statusFilter" class="rounded-md border-gray-300 shadow-sm text-sm">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="review">Menunggu Review</option>
            <option value="published">Published</option>
            <option value="inactive">Inactive</option>
            <option value="archived">Archived</option>
        </select>
        <select wire:model.live="categoryFilter" class="rounded-md border-gray-300 shadow-sm text-sm">
            <option value="">Semua Kategori</option>
            <option value="surat_magang">Surat Magang</option>
            <option value="dokumen_sidang">Dokumen Sidang</option>
            <option value="surat_umum">Surat Umum</option>
            <option value="sertifikat">Sertifikat</option>
        </select>
    </div>

    {{-- Cards View --}}
    @forelse($templates as $tpl)
    <div class="bg-white shadow rounded-lg p-5 mb-4 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            {{-- Left: Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-base font-semibold text-gray-900 truncate">{{ $tpl->name }}</h3>
                    @php
                        $statusColors = [
                            'draft' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'review' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'published' => 'bg-green-100 text-green-800 border-green-300',
                            'inactive' => 'bg-gray-100 text-gray-600 border-gray-300',
                            'archived' => 'bg-red-100 text-red-800 border-red-300',
                        ];
                        $editorLabels = ['flow' => 'Flow', 'canvas' => 'Canvas', 'overlay' => 'Overlay'];
                        $editorColors = ['flow' => 'bg-indigo-50 text-indigo-700', 'canvas' => 'bg-purple-50 text-purple-700', 'overlay' => 'bg-orange-50 text-orange-700'];
                    @endphp
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full border {{ $statusColors[$tpl->status] ?? 'bg-gray-100' }}">
                        {{ ucfirst($tpl->status) }}
                    </span>
                    <span class="px-2 py-0.5 text-xs font-medium rounded {{ $editorColors[$tpl->editor_type] ?? 'bg-gray-100' }}">
                        {{ $editorLabels[$tpl->editor_type] ?? $tpl->editor_type }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-500">
                    <span title="Kode"><strong>Kode:</strong> <code class="bg-gray-100 px-1 rounded">{{ $tpl->type }}</code></span>
                    <span title="Kategori"><strong>Kategori:</strong> {{ str_replace('_', ' ', ucfirst($tpl->category ?? '-')) }}</span>
                    <span title="Versi"><strong>Versi:</strong> v{{ $tpl->activeVersion?->version_number ?? '?' }}</span>
                    <span title="Kop Surat"><strong>Kop:</strong> {{ $tpl->letterheadVersion?->master?->name ?? 'Tanpa Kop' }}</span>
                    <span title="Dokumen terbuat"><strong>Dokumen:</strong> {{ $tpl->instances_count }}</span>
                </div>
                @if($tpl->document_purpose)
                    <div class="mt-2">
                        <span class="px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded border border-blue-200 font-mono">{{ $availablePurposes[$tpl->document_purpose] ?? $tpl->document_purpose }}</span>
                    </div>
                @else
                    <div class="mt-2">
                        <span class="text-xs text-gray-400 italic">Peruntukan belum diatur</span>
                    </div>
                @endif
                <div class="mt-1 text-xs text-gray-400">
                    Dibuat oleh {{ $tpl->creator?->name ?? '-' }} · {{ $tpl->created_at?->diffForHumans() }}
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="flex items-center gap-2 ml-4 flex-shrink-0">
                {{-- Preview --}}
                <a href="{{ route('admin.document-templates.edit', $tpl->id) }}?preview=1" title="Preview"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>

                {{-- Edit --}}
                @if($tpl->status === 'draft')
                <a href="{{ route('admin.document-templates.edit', $tpl->id) }}" title="Edit Draft"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                @endif

                {{-- Dropdown menu --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" title="Aksi lainnya"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-100 hover:text-gray-700 transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 z-50 py-1" style="display: none;">

                        <div class="px-3 py-2 border-b border-gray-100">
                            <p class="text-xs font-semibold text-gray-400 uppercase">Kelola</p>
                        </div>

                        <button wire:click="createNewVersion({{ $tpl->id }})" @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Buat Versi Baru
                        </button>
                        <button wire:click="duplicate({{ $tpl->id }})" @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Duplikat
                        </button>
                        <button wire:click="openPurposeModal({{ $tpl->id }})" @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            Atur Peruntukan
                        </button>
                        <button wire:click="showUsage({{ $tpl->id }})" @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            Lihat Penggunaan
                        </button>

                        <div class="border-t border-gray-100 my-1"></div>
                        <div class="px-3 py-2">
                            <p class="text-xs font-semibold text-gray-400 uppercase">Status</p>
                        </div>

                        @if($tpl->status === 'draft')
                        <button wire:click="publish({{ $tpl->id }})" @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-green-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Publish
                        </button>
                        @endif
                        @if($tpl->status === 'published')
                        <button wire:click="deactivate({{ $tpl->id }})" @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-orange-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            Nonaktifkan
                        </button>
                        @endif
                        @if($tpl->status !== 'archived')
                        <button wire:click="archive({{ $tpl->id }})" @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            Arsipkan
                        </button>
                        @endif

                        <div class="border-t border-gray-100 my-1"></div>
                        <button wire:click="delete({{ $tpl->id }})" wire:confirm="Yakin ingin menghapus template ini? Tindakan ini tidak dapat dibatalkan." @click="open = false"
                                class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        <p class="text-gray-500 mb-4">Belum ada template surat.</p>
        <a href="{{ route('admin.document-templates.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
            + Buat Template Pertama
        </a>
    </div>
    @endforelse

    {{-- Purpose Modal --}}
    @if($showPurposeModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 p-6">
            <h3 class="text-lg font-semibold mb-4">Atur Peruntukan Template</h3>
            <select wire:model="purposeValue" class="w-full rounded-md border-gray-300 shadow-sm text-sm mb-4">
                <option value="">-- Pilih Peruntukan --</option>
                @foreach($availablePurposes as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-400 mb-4">Peruntukan menentukan di mana template ini digunakan secara otomatis oleh sistem (misal: saat generate F1, F2, dll).</p>
            <div class="flex justify-end gap-3">
                <button wire:click="$set('showPurposeModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Batal</button>
                <button wire:click="savePurpose" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Usage Modal --}}
    @if($showUsageModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm mx-4 p-6">
            <h3 class="text-lg font-semibold mb-2">Penggunaan Template</h3>
            <p class="text-sm text-gray-600 mb-4">Template ini digunakan oleh <strong>{{ $usageCount }}</strong> dokumen.</p>
            <div class="flex justify-end">
                <button wire:click="$set('showUsageModal', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>
