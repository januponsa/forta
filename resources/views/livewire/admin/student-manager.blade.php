<div>
    @section('title', 'Manajemen Data Mahasiswa')

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Daftar Mahasiswa (Total Aktif: {{ $totalActive }})</h2>
            <p class="text-sm text-slate-500 mt-1">
                Kelola data mahasiswa, username, password, angkatan, status akademik, suspend login, dan penghapusan akun.
            </p>
        </div>

        <button wire:click="createStudent" class="bg-teal-600 hover:bg-teal-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Mahasiswa
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg relative mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg relative mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 flex flex-col md:flex-row gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIM, Nama, Username, atau Email..." class="border-slate-300 rounded-lg shadow-sm w-full md:w-1/3 py-2 px-3 text-slate-700 text-sm focus:border-teal-500 focus:ring focus:ring-teal-200">
        
        <select wire:model.live="angkatanFilter" class="border-slate-300 rounded-lg shadow-sm py-2 px-3 text-slate-700 text-sm focus:border-teal-500 focus:ring focus:ring-teal-200">
            <option value="">Semua Angkatan</option>
            @foreach($angkatans as $ang)
                <option value="{{ $ang }}">{{ $ang }}</option>
            @endforeach
        </select>

        <select wire:model.live="statusFilter" class="border-slate-300 rounded-lg shadow-sm py-2 px-3 text-slate-700 text-sm focus:border-teal-500 focus:ring focus:ring-teal-200">
            <option value="">Status: Semua Aktif &amp; Suspended</option>
            <option value="active">Status: Hanya Aktif</option>
            <option value="disabled">Status: Hanya Suspended / Nonaktif Login</option>
            <option value="archived">Status: Diarsipkan (Soft Deleted)</option>
        </select>
    </div>

    <div class="bg-white shadow-sm border border-slate-200 overflow-hidden sm:rounded-xl mb-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama &amp; Username</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Angkatan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Akademik</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Akun</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($students as $student)
                        <tr wire:key="student-{{ $student->id }}" class="{{ $student->trashed() ? 'bg-slate-50' : 'hover:bg-slate-50 transition-colors' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-mono font-semibold">{{ $student->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-900">{{ $student->name }}</div>
                                <div class="text-xs text-slate-400 font-mono">@<span>{{ $student->username ?: explode('@', $student->email)[0] }}</span></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $student->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-medium">{{ $student->angkatan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($student->trashed())
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Diarsipkan
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        {{ $student->status_akademik ?: 'Aktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($student->trashed())
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-700">
                                        Login Dinonaktifkan
                                    </span>
                                @elseif ($student->login_enabled)
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                        {{ $student->status_akun ?: 'Login Diizinkan' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                        {{ $student->status_akun ?: 'Login Dinonaktifkan' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button wire:click="editStudent({{ $student->id }})" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold py-1.5 px-2.5 rounded-md border border-indigo-200 transition">
                                        Edit
                                    </button>

                                    @if ($student->trashed())
                                        <button wire:click="restoreStudent({{ $student->id }})" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold py-1.5 px-2.5 rounded-md transition">
                                            Restore
                                        </button>
                                        
                                        @can('users.delete_permanently')
                                            <button wire:click="startDeleteStudent({{ $student->id }})" class="bg-red-800 hover:bg-red-900 text-white text-xs font-semibold py-1.5 px-2.5 rounded-md transition">
                                                Hapus Permanen
                                            </button>
                                        @else
                                            <button wire:click="deleteStudent({{ $student->id }})" wire:confirm="Yakin ingin menghapus permanen mahasiswa {{ $student->name }} ({{ $student->nim }})?" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold py-1.5 px-2.5 rounded-md transition">
                                                Hapus
                                            </button>
                                        @endcan
                                    @else
                                        @if ($student->login_enabled)
                                            <button wire:click="disableStudent({{ $student->id }})" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold py-1.5 px-2.5 rounded-md transition" title="Suspend / Nonaktifkan Login">
                                                Suspend
                                            </button>
                                        @else
                                            <button wire:click="enableStudent({{ $student->id }})" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold py-1.5 px-2.5 rounded-md transition" title="Aktifkan Login Kembali">
                                                Aktifkan
                                            </button>
                                        @endif

                                        <button wire:click="archiveStudent({{ $student->id }})" class="bg-slate-500 hover:bg-slate-600 text-white text-xs font-semibold py-1.5 px-2.5 rounded-md transition">
                                            Arsipkan
                                        </button>

                                        <button wire:click="deleteStudent({{ $student->id }})" wire:confirm="Yakin ingin menghapus mahasiswa {{ $student->name }} ({{ $student->nim }}) beserta datanya?" class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-semibold py-1.5 px-2.5 rounded-md transition">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500">Tidak ada data mahasiswa yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $students->links() }}

    {{-- Create / Edit Student Modal --}}
    @if ($isModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="student-modal-title" role="dialog" aria-modal="true" wire:ignore.self>
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-800 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg leading-6 font-bold text-slate-900" id="student-modal-title">
                        {{ $studentId ? 'Edit Data Mahasiswa' : 'Tambah Mahasiswa Baru' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveStudent">
                    <div class="bg-slate-50 px-4 py-5 sm:p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">NIM <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="nim" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3 font-mono">
                                @error('nim') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Angkatan <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="angkatan" placeholder="Contoh: 2024" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                                @error('angkatan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="name" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Username Login</label>
                                <input type="text" wire:model="username" placeholder="Otomatis dari email jika kosong" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3 font-mono">
                                @error('username') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Email Institusi <span class="text-red-500">*</span></label>
                                <input type="email" wire:model="email" placeholder="nama@student.pradita.ac.id" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                                @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Password Login</label>
                            <input type="password" wire:model="password" placeholder="{{ $studentId ? 'Biarkan kosong jika tidak ingin mengubah password' : 'Kosongkan untuk password default: password / NIM' }}" class="mt-1 focus:ring-teal-500 focus:border-teal-500 block w-full shadow-sm sm:text-sm border-slate-300 rounded-md py-2 px-3">
                            <p class="text-xs text-slate-500 mt-1">Mahasiswa dapat login menggunakan NIM, Username, atau Email beserta password ini di halaman login.</p>
                            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-200">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Status Akademik</label>
                                <select wire:model="status_akademik" class="mt-1 block w-full py-2 px-3 border border-slate-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Cuti">Cuti</option>
                                    <option value="Lulus">Lulus</option>
                                    <option value="Nonaktif">Nonaktif</option>
                                </select>
                                @error('status_akademik') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700">Persetujuan</label>
                                <select wire:model="approval_status" class="mt-1 block w-full py-2 px-3 border border-slate-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                                    <option value="approved">Disetujui (Approved)</option>
                                    <option value="pending">Menunggu (Pending)</option>
                                    <option value="rejected">Ditolak (Rejected)</option>
                                </select>
                                @error('approval_status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Akses Login</label>
                                <label class="inline-flex items-center cursor-pointer mt-1">
                                    <input type="checkbox" wire:model="login_enabled" class="sr-only peer">
                                    <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600"></div>
                                    <span class="ml-2 text-xs font-semibold text-slate-700">{{ $login_enabled ? 'Aktif' : 'Suspended' }}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan Perubahan
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Deletion Confirmation Modal --}}
    @if ($confirmingStudentDeletion)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full shadow-xl mx-4">
                <h3 class="text-lg font-bold text-red-600 mb-2">Hapus Mahasiswa Secara Permanen</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Apakah Anda yakin ingin menghapus mahasiswa <strong>{{ \App\Models\Student::withTrashed()->find($studentIdBeingDeleted)?->name }}</strong> secara permanen? 
                    Tindakan ini akan menghapus seluruh data pendaftaran, berkas tugas akhir/magang yang diunggah, dan log aktivitas mahasiswa tersebut secara fisik dari disk. 
                    Tindakan ini <span class="text-red-600 font-bold">tidak dapat dibatalkan</span>.
                </p>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ketik <span class="font-mono text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded">HAPUS {{ \App\Models\Student::withTrashed()->find($studentIdBeingDeleted)?->nim }}</span> untuk mengonfirmasi:
                    </label>
                    <input type="text" wire:model.live="nimConfirmInput" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline font-mono" placeholder="Ketik di sini...">
                </div>

                <div class="flex justify-end gap-2">
                    <button wire:click="cancelDeleteStudent" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none text-sm">
                        Batal
                    </button>
                    <button wire:click="deleteStudentPermanently" 
                        @if($nimConfirmInput !== 'HAPUS ' . \App\Models\Student::withTrashed()->find($studentIdBeingDeleted)?->nim) disabled @endif
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none text-sm @if($nimConfirmInput !== 'HAPUS ' . \App\Models\Student::withTrashed()->find($studentIdBeingDeleted)?->nim) opacity-50 cursor-not-allowed @endif">
                        Hapus Permanen
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
