<div>
    @section('title', 'Manajemen Sidang - Dashboard')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Dashboard Ringkasan Sidang Magang/KP
        </h3>
        <div>
            <select wire:model.live="semesterFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                <option value="">Semua Semester</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem }}">{{ $sem }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Statistik Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Pendaftar Sidang</dt>
                    <dd class="mt-1 text-3xl font-semibold text-indigo-600">{{ $stats['total'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Menunggu Verifikasi</dt>
                    <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $stats['menunggu_verifikasi'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Belum Dijadwalkan</dt>
                    <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $stats['belum_dijadwalkan'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Sudah Dijadwalkan</dt>
                    <dd class="mt-1 text-3xl font-semibold text-blue-600">{{ $stats['sudah_dijadwalkan'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-400">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Tunggu Nilai Penguji</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['menunggu_nilai_penguji'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-400">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Tunggu Nilai Pembimbing</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['menunggu_nilai_pembimbing'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-yellow-400">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Tunggu Nilai Mentor</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['menunggu_nilai_mentor'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-500">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Siap Finalisasi</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['siap_finalisasi'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-orange-500">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Revisi Belum Selesai</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['revisi_belum_selesai'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-green-700">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Lulus Sidang</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['lulus'] }}</dd>
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow rounded-lg border-l-4 border-red-700">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-xs font-medium text-gray-500 uppercase">Tidak Lulus</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $stats['tidak_lulus'] }}</dd>
                </div>
            </div>
        </div>

        <!-- Filter & Search Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                <div class="w-full sm:w-1/3">
                    <input type="text" wire:model.live="search" placeholder="Cari Nama/NIM..." class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                </div>
                <div class="w-full sm:w-1/3">
                    <select wire:model.live="filterStatus" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">-- Semua Status --</option>
                        <option value="registered">Terdaftar / Menunggu Verifikasi Dokumen</option>
                        <option value="waiting_schedule">Menunggu Jadwal</option>
                        <option value="scheduled">Sudah Dijadwalkan</option>
                        <option value="ready_to_finalize">Siap Finalisasi</option>
                        <option value="passed">Lulus</option>
                        <option value="failed">Tidak Lulus</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal & Ruang</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tim Penilai</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Sidang</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cases as $case)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $case->student->name ?? 'Unknown' }}</div>
                                <div class="text-sm text-gray-500">{{ $case->student->nim ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($case->latestSchedule)
                                    <div class="text-sm text-gray-900">{{ $case->latestSchedule->date->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $case->latestSchedule->start_time->format('H:i') }} - {{ $case->latestSchedule->room_or_link }}</div>
                                @else
                                    <span class="text-xs text-red-500">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-700">P: {{ $case->assignments->where('role', 'supervisor')->first()->lecturer->name ?? '-' }}</div>
                                <div class="text-xs text-gray-700">U: {{ $case->assignments->where('role', 'examiner')->first()->lecturer->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ str_replace('_', ' ', Str::title($case->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($case->final_score)
                                    <span class="text-lg font-bold text-gray-900">{{ $case->final_score }}</span>
                                    <span class="text-xs text-gray-500">({{ $case->final_grade }})</span>
                                @else
                                    <span class="text-xs text-gray-400">Belum lengkap</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                Tidak ada data pendaftar sidang untuk kriteria ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $cases->links() }}
            </div>
        </div>
    </div>
</div>
