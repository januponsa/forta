<div>
    @section('title', 'Manajemen Dosen')

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Dosen</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola data dosen dan unggah tanda tangan digital.</p>
        </div>
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Dosen
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg relative mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('message') }}
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg relative mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
        <div class="w-full md:w-1/3 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIP, atau email..." class="pl-10 w-full border-slate-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm py-2.5">
        </div>
    </div>

    <div class="bg-white shadow-sm border border-slate-200 overflow-hidden sm:rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-1/3">Informasi Dosen</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanda Tangan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($lecturers as $lecturer)
                        <tr wire:key="lecturer-{{ $lecturer->id }}" class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold uppercase overflow-hidden">
                                        {{ substr($lecturer->name, 0, 2) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-slate-900">{{ $lecturer->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $lecturer->email }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">NIP: <span>{{ $lecturer->nip ?? '-' }}</span></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lecturer->signature_path)
                                    <div class="h-12 w-24 bg-gray-50 border border-gray-200 rounded flex items-center justify-center overflow-hidden p-1">
                                        <img src="{{ Storage::url($lecturer->signature_path) }}" alt="TTD" class="max-h-full max-w-full object-contain">
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lecturer->is_active)
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 self-center"></span> Aktif
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5 self-center"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $lecturer->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-md transition-colors mr-2">Edit & TTD</button>
                                <button wire:click="delete({{ $lecturer->id }})" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors" wire:confirm="Yakin ingin menghapus dosen ini?">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 whitespace-nowrap text-sm text-slate-500 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                @if($search)
                                    Tidak ada dosen yang sesuai dengan kata kunci "{{ $search }}".
                                @else
                                    Belum ada data dosen.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $lecturers->links() }}
    </div>

    <!-- Modal Form -->
    @if($isOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" wire:ignore.self>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-800 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100">
                    <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                        {{ $lecturerId ? 'Edit Dosen' : 'Tambah Dosen Baru' }}
                    </h3>
                </div>
                <form wire:submit.prevent="store">
                    <div class="bg-slate-50 px-4 py-5 sm:p-6 space-y-4">
                        
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="nip" class="block text-sm font-semibold text-slate-700">NIP</label>
                            <input type="text" wire:model="nip" id="nip" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                            @error('nip') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                            <input type="email" wire:model="email" id="email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-semibold text-slate-700">Jabatan Struktural (Opsional)</label>
                            <input type="text" wire:model="position" id="position" placeholder="Contoh: Kepala Program Studi" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                            @error('position') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            <p class="mt-1 text-xs text-gray-500">Isi hanya jika dosen ini memiliki jabatan fungsional/struktural yang memerlukan tanda tangan dokumen resmi (Request Mahasiswa).</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Unggah Tanda Tangan (Admin Upload)</label>
                            
                            @if($existingSignaturePath)
                            <div class="mb-3 flex flex-col items-center p-3 bg-white border border-gray-200 rounded">
                                <span class="text-xs text-gray-500 mb-2">Tanda Tangan Saat Ini:</span>
                                <img src="{{ Storage::url($existingSignaturePath) }}" alt="TTD" class="max-h-16 object-contain">
                            </div>
                            @endif

                            <div class="mt-1 flex items-center">
                                <input type="file" wire:model="signature" id="signature-upload" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100
                                "/>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Gunakan format gambar transparan (PNG). Maks 2MB.</p>
                            @error('signature') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="signature" class="mt-1 text-xs text-indigo-600">Mengunggah file...</div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Unggah Cap Stempel (Hanya jika memiliki jabatan & stempel Institusi/Prodi)</label>
                            
                            @if($existingStampPath)
                            <div class="mb-3 flex flex-col items-center p-3 bg-white border border-gray-200 rounded">
                                <span class="text-xs text-gray-500 mb-2">Cap Stempel Saat Ini:</span>
                                <img src="{{ Storage::url($existingStampPath) }}" alt="Stempel" class="max-h-16 object-contain">
                            </div>
                            @endif

                            <div class="mt-1 flex items-center">
                                <input type="file" wire:model="stamp" id="stamp-upload" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-50 file:text-indigo-700
                                    hover:file:bg-indigo-100
                                "/>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Gunakan format gambar transparan (PNG). Maks 2MB.</p>
                            @error('stamp') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="stamp" class="mt-1 text-xs text-indigo-600">Mengunggah file...</div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-200">
                            <label class="block text-sm font-semibold text-slate-700 mb-3">Pengaturan Tampilan TTD (Untuk Surat Resmi)</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="include_name" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Tampilkan Nama Tercetak di Bawah TTD</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="include_position" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Tampilkan Jabatan di Bawah TTD</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="include_date" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Sertakan Tanggal TTD / Timestamp</span>
                                </label>
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Status Dosen</label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                <span class="ml-3 text-sm font-medium text-slate-700">{{ $is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </label>
                        </div>

                    </div>
                    <div class="bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors disabled:opacity-50" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="store">Simpan Data</span>
                            <span wire:loading wire:target="store">Menyimpan...</span>
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
