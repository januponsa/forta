<div>
    @section('title', 'Manajemen Sidang - Penjadwalan & Penugasan')
    
    <!-- Header Section -->
    <div class="px-6 py-5 bg-white border-b border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Penjadwalan & Penugasan Sidang Magang/KP
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Atur jadwal waktu, ruangan, serta penugasan dosen pembimbing dan penguji sidang (dilengkapi deteksi otomatis bentrok jadwal).
                </p>
            </div>
            
            <!-- Filters & Search -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Search Input -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIM..." class="pl-9 pr-3 py-2 text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm w-48 sm:w-56">
                </div>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter" class="py-2 px-3 text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm bg-white">
                    <option value="">Semua Status Jadwal</option>
                    <option value="scheduled">Sudah Dijadwalkan</option>
                    <option value="unscheduled">Belum Dijadwalkan</option>
                </select>

                <!-- Semester Filter -->
                <select wire:model.live="semesterFilter" class="py-2 px-3 text-xs rounded-lg border-slate-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-sm bg-white">
                    <option value="">Semua Semester</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem }}">{{ $sem }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="mb-5 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-emerald-800">{{ session('message') }}</p>
                </div>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-5 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Mahasiswa & Judul</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu & Tempat</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dosen Penilai</th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Sidang</th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi Jadwal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($cases as $case)
                            @php
                                $schedule = $case->latestSchedule;
                                $supervisor = $case->assignments->where('role', 'supervisor')->first();
                                $examiner = $case->assignments->where('role', 'examiner')->first();
                                $isScheduled = !empty($schedule);
                            @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <!-- Mahasiswa Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                        {{ substr($case->student->name ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">{{ $case->student->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-indigo-600 font-mono font-medium">{{ $case->student->nim ?? '-' }}</div>
                                        <div class="text-xs text-slate-500 mt-1 line-clamp-1 italic font-serif">"{{ $case->metadata['report_title'] ?? 'Laporan KP' }}"</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">Mitra: {{ $case->metadata['company_name'] ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Jadwal & Ruang -->
                            <td class="px-6 py-4">
                                @if($isScheduled)
                                    <div class="space-y-1">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $schedule->date ? ($schedule->date instanceof \Carbon\Carbon ? $schedule->date->format('d/m/Y') : substr($schedule->date, 0, 10)) : '-' }}
                                        </div>
                                        <div class="text-xs text-slate-600 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $schedule->start_time ? ($schedule->start_time instanceof \Carbon\Carbon ? $schedule->start_time->format('H:i') : substr($schedule->start_time, 0, 5)) : '-' }} - {{ $schedule->end_time ? ($schedule->end_time instanceof \Carbon\Carbon ? $schedule->end_time->format('H:i') : substr($schedule->end_time, 0, 5)) : '-' }} WIB
                                        </div>
                                        <div class="text-xs font-medium text-slate-700 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            {{ $schedule->room_or_link ?? '-' }}
                                        </div>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Dijadwalkan
                                    </span>
                                @endif
                            </td>

                            <!-- Penilai (Dosen) -->
                            <td class="px-6 py-4">
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-800">Pembimbing</span>
                                        <span class="text-slate-800 font-medium truncate max-w-[180px]">{{ $supervisor->lecturer->name ?? '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-800">Penguji</span>
                                        <span class="text-slate-800 font-medium truncate max-w-[180px]">{{ $examiner->lecturer->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Status Sidang -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusBadge = match($case->status) {
                                        'registered' => 'bg-slate-100 text-slate-800 border-slate-200',
                                        'waiting_schedule' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'scheduled' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'waiting_mentor_score' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'waiting_scores' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'ready_to_finalize' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                        'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'revision_required' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        default => 'bg-slate-100 text-slate-800 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border {{ $statusBadge }}">
                                    {{ $case->status }}
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium space-x-2">
                                @if($isScheduled)
                                    <!-- Edit Jadwal -->
                                    <button type="button" wire:click="openModal({{ $case->id }})" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>

                                    <!-- Hapus Jadwal -->
                                    <button type="button" wire:click="deleteSchedule({{ $case->id }})" wire:confirm="Apakah Anda yakin ingin membatalkan dan menghapus jadwal sidang untuk {{ $case->student->name }}?" class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                @else
                                    <!-- Buat Jadwal Baru -->
                                    <button type="button" wire:click="openModal({{ $case->id }})" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Atur Jadwal
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm font-medium text-slate-600">Tidak ada data peserta sidang yang ditemukan.</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Tarik Pendaftar Baru" pada menu Peserta Sidang jika belum ada pendaftar.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Jadwal Sidang (Create / Edit + Conflict Detection) -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl mx-auto flex flex-col max-h-[90vh] border border-slate-100 overflow-hidden transform transition-all animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        @if($hasExistingSchedule)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-blue-100 text-blue-800">Edit Mode</span>
                            Edit Jadwal Sidang
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-800">Baru</span>
                            Atur Jadwal Sidang
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">
                        {{ $studentName }} ({{ $studentNim }})
                    </p>
                </div>
                <button type="button" wire:click="closeModal" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200 transition-colors">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Modal Body Form -->
            <form wire:submit="saveSchedule" class="flex-1 overflow-y-auto">
                <div class="px-6 py-5 space-y-4 text-xs">
                    
                    <!-- Student Summary Card -->
                    <div class="p-3 bg-indigo-50/70 border border-indigo-100 rounded-xl space-y-1">
                        <div class="text-[11px] font-semibold text-indigo-900 uppercase tracking-wide">Detail Mahasiswa</div>
                        <div class="text-xs font-medium text-slate-800">{{ $studentName }} — <span class="font-mono">{{ $studentNim }}</span></div>
                        <div class="text-[11px] text-slate-600 line-clamp-2 italic">"{{ $reportTitle }}"</div>
                    </div>

                    <!-- Peringatan Deteksi Bentrok Global (Jika Ada) -->
                    @if($supervisorConflict || $examinerConflict || $roomConflict)
                        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-xl space-y-2 animate-in fade-in duration-150">
                            <div class="flex items-center gap-2 text-rose-800 font-bold text-xs">
                                <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Terdeteksi Bentrok Jadwal!
                            </div>
                            <ul class="text-[11px] text-rose-700 space-y-1 list-disc list-inside">
                                @if($supervisorConflict)
                                    <li><strong>Dosen Pembimbing:</strong> {{ $supervisorConflict['message'] }}</li>
                                @endif
                                @if($examinerConflict)
                                    <li><strong>Dosen Penguji:</strong> {{ $examinerConflict['message'] }}</li>
                                @endif
                                @if($roomConflict)
                                    <li><strong>Ruangan:</strong> {{ $roomConflict['message'] }}</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <!-- Tanggal Sidang -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Tanggal Sidang <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" wire:model.live="date" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-xs py-2 px-3">
                        @error('date') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Waktu Mulai & Selesai -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Waktu Mulai <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" wire:model.live="start_time" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-xs py-2 px-3">
                            @error('start_time') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Waktu Selesai <span class="text-rose-500">*</span>
                            </label>
                            <input type="time" wire:model.live="end_time" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-xs py-2 px-3">
                            @error('end_time') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <!-- Ruang / Link Daring -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Ruang Sidang / Link Daring <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" wire:model.live.debounce.300ms="room_or_link" placeholder="Contoh: Ruang Sidang 101 atau Link Google Meet" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-xs py-2 px-3">
                        
                        <!-- Quick Pick Ruangan -->
                        <div class="mt-2 flex flex-wrap gap-1.5 items-center">
                            <span class="text-[11px] text-slate-400">Pilihan cepat:</span>
                            <button type="button" wire:click="setQuickRoom('Ruang Sidang 101')" class="px-2 py-0.5 rounded text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">R. 101</button>
                            <button type="button" wire:click="setQuickRoom('Ruang Sidang 202')" class="px-2 py-0.5 rounded text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">R. 202</button>
                            <button type="button" wire:click="setQuickRoom('Lab Komputer')" class="px-2 py-0.5 rounded text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">Lab Komputer</button>
                            <button type="button" wire:click="setQuickRoom('Online via Google Meet')" class="px-2 py-0.5 rounded text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">Google Meet</button>
                        </div>
                        @error('room_or_link') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <hr class="border-slate-200 my-2">
                    
                    <!-- Dosen Pembimbing -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700">
                                Dosen Pembimbing <span class="text-rose-500">*</span>
                            </label>
                            @if($supervisorConflict)
                                <span class="text-[10px] font-semibold text-rose-600 flex items-center gap-1">
                                    ⚠️ Bentrok Jadwal
                                </span>
                            @endif
                        </div>
                        <select wire:model.live="supervisor_id" class="block w-full rounded-lg {{ $supervisorConflict ? 'border-rose-400 bg-rose-50/40 text-rose-900 focus:border-rose-500 focus:ring-rose-200' : 'border-slate-300 bg-white focus:border-indigo-500 focus:ring-indigo-200' }} shadow-sm text-xs py-2 px-3">
                            <option value="">-- Pilih Dosen Pembimbing --</option>
                            @foreach($lecturers as $lec)
                                @php $isBusy = in_array($lec->id, $busyLecturerIds); @endphp
                                <option value="{{ $lec->id }}">
                                    {{ $lec->name }} (NIP: {{ $lec->nip }}) {{ $isBusy ? ' — [⚠️ BENTROK]' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('supervisor_id') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Dosen Penguji -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-semibold text-slate-700">
                                Dosen Penguji / Ketua Sidang <span class="text-rose-500">*</span>
                            </label>
                            @if($examinerConflict)
                                <span class="text-[10px] font-semibold text-rose-600 flex items-center gap-1">
                                    ⚠️ Bentrok Jadwal
                                </span>
                            @endif
                        </div>
                        <select wire:model.live="examiner_id" class="block w-full rounded-lg {{ $examinerConflict ? 'border-rose-400 bg-rose-50/40 text-rose-900 focus:border-rose-500 focus:ring-rose-200' : 'border-slate-300 bg-white focus:border-indigo-500 focus:ring-indigo-200' }} shadow-sm text-xs py-2 px-3">
                            <option value="">-- Pilih Dosen Penguji --</option>
                            @foreach($lecturers as $lec)
                                @php $isBusy = in_array($lec->id, $busyLecturerIds); @endphp
                                <option value="{{ $lec->id }}">
                                    {{ $lec->name }} (NIP: {{ $lec->nip }}) {{ $isBusy ? ' — [⚠️ BENTROK]' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('examiner_id') <span class="text-rose-600 text-[11px] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <div>
                        @if($hasExistingSchedule)
                            <button type="button" wire:click="deleteSchedule({{ $caseId }})" wire:confirm="Yakin ingin membatalkan dan menghapus jadwal sidang ini?" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-700 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus Jadwal
                            </button>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 border border-slate-300 text-xs font-semibold rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                            Batal
                        </button>
                        <button type="submit" wire:loading.attr="disabled" @if($supervisorConflict || $examinerConflict || $roomConflict) disabled @endif class="inline-flex items-center gap-1.5 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="saveSchedule">Simpan Jadwal</span>
                            <span wire:loading wire:target="saveSchedule" class="inline-flex items-center gap-1">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
