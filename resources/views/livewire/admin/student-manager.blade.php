<div>
    @section('title', 'Manajemen Data Mahasiswa')

    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Mahasiswa (Total Aktif: {{ $totalActive }})</h2>
        
        <div class="text-sm text-gray-500">
            * Data mahasiswa dikelola melalui sinkronisasi roster resmi atau manajemen manual admin.
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

    <div class="mb-4 flex flex-col md:flex-row gap-4">
        <input type="text" wire:model.live="search" placeholder="Cari berdasarkan NIM, Nama, atau Email..." class="shadow border rounded w-full md:w-1/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        
        <select wire:model.live="angkatanFilter" class="shadow border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Semua Angkatan</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
        </select>

        <select wire:model.live="statusFilter" class="shadow border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Status: Semua Aktif & Nonaktif</option>
            <option value="active">Status: Hanya Aktif</option>
            <option value="disabled">Status: Hanya Nonaktif Login</option>
            <option value="archived">Status: Diarsipkan (Soft Deleted)</option>
        </select>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Angkatan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Akademik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Akun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $student)
                    <tr class="{{ $student->trashed() ? 'bg-gray-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-mono">{{ $student->nim }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->angkatan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($student->trashed())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Diarsipkan
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $student->status_akademik }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($student->trashed())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Login Dinonaktifkan
                                </span>
                            @elseif ($student->login_enabled)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $student->status_akun }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $student->status_akun }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
                            @if ($student->trashed())
                                <button wire:click="restoreStudent({{ $student->id }})" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-1 px-2 rounded">
                                    Restore
                                </button>
                                
                                @can('users.delete_permanently')
                                    <button wire:click="startDeleteStudent({{ $student->id }})" class="bg-red-800 hover:bg-red-900 text-white text-xs font-bold py-1 px-2 rounded">
                                        Hapus Permanen
                                    </button>
                                @endcan
                            @else
                                @if ($student->login_enabled)
                                    <button wire:click="disableStudent({{ $student->id }})" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-1 px-2 rounded">
                                        Nonaktifkan Login
                                    </button>
                                @else
                                    <button wire:click="enableStudent({{ $student->id }})" class="bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-1 px-2 rounded">
                                        Aktifkan Login
                                    </button>
                                @endif

                                <button wire:click="archiveStudent({{ $student->id }})" class="bg-red-500 hover:bg-red-600 text-white text-xs font-bold py-1 px-2 rounded">
                                    Arsipkan
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $students->links() }}

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
