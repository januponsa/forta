<div>
    @section('title', 'Kelola Form Akademik')

    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Daftar Form</h2>
        <div class="flex items-center gap-3">
            <button wire:click="openSemesterModal" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-bold py-2 px-4 rounded border border-indigo-300 shadow-sm transition-colors">
                Kelola Semester
            </button>
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-sm transition-colors">
                Tambah Form
            </button>
        </div>
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

    <div class="bg-white p-4 rounded shadow mb-4">
        @if(!$isReordering)
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                <select wire:model.live="semesterFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 text-sm">
                    <option value="">Semua Semester</option>
                    @foreach($allSemesters as $sem)
                        <option value="{{ $sem }}">{{ $sem }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 text-sm">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="active">Aktif</option>
                    <option value="closed">Ditutup</option>
                    <option value="archived">Diarsipkan</option>
                </select>
            </div>

            <div class="w-full md:w-1/3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="Cari nama form, kode form, atau jenis kegiatan..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 text-sm">
            </div>

            <div class="w-full md:w-auto flex flex-col md:flex-row gap-2 mt-2 md:mt-0 md:ml-auto">
                <button wire:click="clearFilters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded w-full md:w-auto text-sm h-10 whitespace-nowrap">
                    Bersihkan Filter
                </button>
                <button wire:click="enableReorderMode" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded w-full md:w-auto text-sm h-10 whitespace-nowrap shadow-sm">
                    Atur Urutan
                </button>
            </div>
        </div>
        
        <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <label>Tampilkan:</label>
                <select wire:model.live="perPage" class="border-gray-300 rounded-md shadow-sm text-sm py-1">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">Semua</option>
                </select>
            </div>
            <div>
                @if($perPage === 'all')
                    Menampilkan seluruh {{ $forms->count() }} form
                @else
                    Menampilkan {{ $forms->firstItem() ?? 0 }}–{{ $forms->lastItem() ?? 0 }} dari {{ $forms->total() ?? 0 }} form
                @endif
            </div>
        </div>
        @else
        <div class="flex flex-col md:flex-row gap-4 items-center justify-between bg-indigo-50 p-4 rounded border border-indigo-200">
            <div>
                <h3 class="font-bold text-indigo-800">Mode Atur Urutan: {{ $semesterFilter }}</h3>
                <p class="text-sm text-indigo-600">Geser ikon (⋮⋮) pada baris untuk mengubah urutan, kemudian klik Simpan.</p>
            </div>
            <div class="flex gap-2">
                <button @click="$dispatch('save-reorder')" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow">
                    Simpan Urutan
                </button>
                <button wire:click="disableReorderMode" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow">
                    Batal
                </button>
            </div>
        </div>
        @endif
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200" id="forms-table">
            <thead class="bg-gray-50">
                <tr>
                    @if($isReordering)
                    <th class="px-6 py-3 w-10"></th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul & Kode Form</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester & Fase</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody 
                class="bg-white divide-y divide-gray-200"
                x-data="{
                    sortableInstance: null,
                    initSortable() {
                        if (typeof Sortable === 'undefined') return;
                        
                        if (this.sortableInstance) {
                            this.sortableInstance.destroy();
                        }
                        
                        if (@js($isReordering)) {
                            this.sortableInstance = new Sortable(this.$refs.tbody, {
                                handle: '.drag-handle',
                                animation: 150,
                            });
                        }
                    },
                    saveOrder() {
                        if (!this.sortableInstance) return;
                        let order = Array.from(this.$refs.tbody.querySelectorAll('tr[data-id]'))
                            .map(row => row.dataset.id);
                        $wire.reorder(order);
                    }
                }"
                x-ref="tbody"
                x-init="
                    if (typeof Sortable === 'undefined') {
                        let script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js';
                        script.onload = () => initSortable();
                        document.head.appendChild(script);
                    } else {
                        initSortable();
                    }
                    $watch('$wire.isReordering', val => initSortable());
                "
                @save-reorder.window="saveOrder()"
            >
                @forelse($forms as $form)
                    <tr wire:key="form-{{ $form->id }}" data-id="{{ $form->id }}" class="{{ $isReordering ? 'hover:bg-indigo-50' : '' }}">
                        @if($isReordering)
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="drag-handle cursor-move text-gray-400 hover:text-gray-600 select-none text-xl" aria-label="Ubah urutan form">⋮⋮</span>
                        </td>
                        @endif
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $form->title }}
                                <span class="ml-1 bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full font-semibold">v{{ $form->version }}</span>
                            </div>
                            <div class="text-xs text-gray-500">{{ $form->form_code ?? '-' }} | {{ $form->activityType->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $form->semester ?? 'Tidak ada' }}</div>
                            <div class="text-xs text-gray-500">Fase: {{ $form->phase ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $form->status == 'active' ? 'bg-green-100 text-green-800' : ($form->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : ($form->status == 'closed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($form->status) }}
                            </span>
                            <div class="text-xs text-gray-500 mt-1">{{ $form->submissions_count }} submissions</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-700">#{{ $form->display_order }}</span>
                                @if(!$isReordering && $semesterFilter)
                                <div class="flex flex-col ml-2">
                                    <button wire:click="moveUp({{ $form->id }})" class="text-gray-400 hover:text-blue-600 focus:outline-none" aria-label="Naik" title="Naik">▲</button>
                                    <button wire:click="moveDown({{ $form->id }})" class="text-gray-400 hover:text-blue-600 focus:outline-none" aria-label="Turun" title="Turun">▼</button>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-1">
                            @if ($form->trashed())
                                <button wire:click="restore({{ $form->id }})" class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-2 py-1 rounded text-xs">Restore</button>
                                <button wire:click="deletePermanently({{ $form->id }})" class="bg-red-800 text-white hover:bg-red-900 px-2 py-1 rounded text-xs" onclick="confirm('Yakin ingin menghapus form ini secara permanen?') || event.stopImmediatePropagation()">Hapus Permanen</button>
                            @else
                                <a href="{{ route('admin.forms.fields', $form->id) }}" class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-2 py-1 rounded text-xs">Fields</a>
                                <button wire:click="createNewVersion({{ $form->id }})" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-2 py-1 rounded text-xs" title="Membuat revisi dari form ini. Memiliki nomor versi baru, namun versi dan jawaban lama tetap dipertahankan sebagai arsip." onclick="confirm('Yakin ingin membuat versi baru dari form ini?') || event.stopImmediatePropagation()">Versi Baru</button>
                                <button wire:click="duplicate({{ $form->id }})" class="bg-green-50 text-green-700 hover:bg-green-100 px-2 py-1 rounded text-xs" title="Membuat form baru yang sepenuhnya independen (Salinan) tanpa menyalin jawaban sebelumnya." onclick="confirm('Yakin ingin menduplikasi form ini?') || event.stopImmediatePropagation()">Duplikat</button>
                                <button wire:click="edit({{ $form->id }})" class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-2 py-1 rounded text-xs">Edit</button>
                                
                                @if($form->status !== 'active')
                                    <button wire:click="activate({{ $form->id }})" class="bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-2 py-1 rounded text-xs">Aktifkan</button>
                                @endif
                                @if($form->status === 'active')
                                    <button wire:click="closeForm({{ $form->id }})" class="bg-orange-50 text-orange-700 hover:bg-orange-100 px-2 py-1 rounded text-xs">Tutup</button>
                                @endif
                                @if($form->status !== 'archived')
                                    <button wire:click="archive({{ $form->id }})" class="bg-slate-100 text-slate-700 hover:bg-slate-200 px-2 py-1 rounded text-xs">Arsipkan</button>
                                @endif
                                
                                <button wire:click="delete({{ $form->id }})" class="bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1 rounded text-xs" onclick="confirm('Yakin ingin menghapus form ini?') || event.stopImmediatePropagation()">Hapus</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            @if($search || $semesterFilter || $statusFilter)
                                Tidak ada form yang sesuai dengan pencarian atau filter.
                            @else
                                Belum ada form.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(!$isReordering && $perPage !== 'all')
    <div class="mt-4">
        {{ $forms->links() }}
    </div>
    @endif

    <!-- Modal -->
    @if($isOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75" wire:click="closeModal"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4 col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Judul Form</label>
                                <input type="text" wire:model="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4 col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                                <textarea wire:model="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Kegiatan</label>
                                <select wire:model="activity_type_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">-- Pilih --</option>
                                    @foreach($activityTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('activity_type_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Fase (Phase)</label>
                                <input type="text" wire:model="phase" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('phase') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Semester</label>
                                <input type="text" wire:model="semester" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('semester') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                <select wire:model="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="closed">Closed</option>
                                    <option value="archived">Archived</option>
                                </select>
                                @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Waktu Buka</label>
                                <input type="datetime-local" wire:model="open_at" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('open_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Waktu Tutup</label>
                                <input type="datetime-local" wire:model="close_at" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                @error('close_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed sm:ml-3 sm:w-auto sm:text-sm">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Kelola Semester -->
    @if($isSemesterModalOpen)
    <div class="fixed inset-0 z-[60] overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeSemesterModal"></div>
        
        <!-- Modal Panel -->
        <div class="relative bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all w-full max-w-5xl flex flex-col max-h-[90vh]">
            <div class="bg-white px-6 pt-5 pb-4 border-b border-gray-100 flex-shrink-0">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900" id="modal-title">
                        Manajemen Semester Akademik
                        </h3>
                        <button wire:click="closeSemesterModal" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
                <div class="p-6 overflow-y-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Form Entry Semester -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded border border-gray-200">
                            <h4 class="font-bold text-sm text-gray-700 mb-3">{{ $semesterId ? 'Edit Semester' : 'Tambah Semester' }}</h4>
                            <form wire:submit.prevent="saveSemester">
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Semester</label>
                                    <input type="text" wire:model="sem_name" class="w-full text-sm border-gray-300 rounded shadow-sm" placeholder="Contoh: Gasal 2024/2025">
                                    @error('sem_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Jenis Semester</label>
                                    <select wire:model="sem_type" class="w-full text-sm border-gray-300 rounded shadow-sm">
                                        <option value="Gasal">Gasal</option>
                                        <option value="Genap">Genap</option>
                                        <option value="Antara">Semester Antara</option>
                                    </select>
                                    @error('sem_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Tahun Akademik</label>
                                    <input type="text" wire:model="sem_academic_year" class="w-full text-sm border-gray-300 rounded shadow-sm" placeholder="Contoh: 2024/2025">
                                    @error('sem_academic_year') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                    <input type="date" wire:model="sem_start_date" class="w-full text-sm border-gray-300 rounded shadow-sm">
                                    @error('sem_start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Selesai</label>
                                    <input type="date" wire:model="sem_end_date" class="w-full text-sm border-gray-300 rounded shadow-sm">
                                    @error('sem_end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3 flex items-center gap-4">
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" wire:model="sem_is_active" class="mr-1">
                                        Aktif
                                    </label>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-xs">Simpan</button>
                                    @if($semesterId)
                                        <button type="button" wire:click="resetSemesterFields" class="w-full bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-xs">Batal</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                        
                        <!-- List Semester -->
                        <div class="md:col-span-2">
                            <div class="overflow-y-auto max-h-[60vh]">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Semester</th>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Mulai</th>
                                            <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Selesai</th>
                                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($calendars as $sem)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 text-sm text-gray-900 font-medium">
                                                {{ $sem->semester_name }}
                                                <div class="text-[10px] text-gray-500">{{ $sem->semester_type }} | {{ $sem->academic_year }}</div>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-500">{{ $sem->start_date ? $sem->start_date->format('d/m/Y') : '-' }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-500">{{ $sem->end_date ? $sem->end_date->format('d/m/Y') : '-' }}</td>
                                            <td class="px-3 py-2 text-sm text-center">
                                                  @if($sem->is_active)
                                                      <button wire:click="toggleSemesterStatus({{ $sem->id }})" class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-green-100 text-green-800 hover:bg-green-200 cursor-pointer transition-colors" title="Klik untuk menonaktifkan">Aktif</button>
                                                  @else
                                                      <button wire:click="toggleSemesterStatus({{ $sem->id }})" class="px-2 inline-flex text-[10px] leading-4 font-semibold rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 cursor-pointer transition-colors" title="Klik untuk mengaktifkan">Nonaktif</button>
                                                  @endif
                                              </td>
                                            <td class="px-3 py-2 text-sm font-medium text-center space-x-1">
                                                <button wire:click="editSemester({{ $sem->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded text-xs">Edit</button>
                                                <button wire:click="deleteSemester({{ $sem->id }})" onclick="confirm('Yakin ingin menghapus semester ini? Form yang terhubung harus dikosongkan dahulu.') || event.stopImmediatePropagation()" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-2 py-1 rounded text-xs">Hapus</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
