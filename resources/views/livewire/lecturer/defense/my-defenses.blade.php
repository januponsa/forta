<div>
    @section('title', 'Sidang Saya')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Daftar Sidang Mahasiswa Bimbingan / Ujian
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
    
    <div class="p-4">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($cases as $case)
                    @php
                        $assignment = $case->assignments->first();
                        $roleLabel = $assignment->role === 'supervisor' ? 'Pembimbing' : ($assignment->role === 'examiner' ? 'Penguji/Ketua' : 'Penguji Tambahan');
                        $schedule = $case->latestSchedule;
                        
                        $assessment = $case->assessments->first();
                        $statusBadge = $assessment && $assessment->status === 'final' 
                            ? '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai (Final)</span>'
                            : '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Final</span>';
                    @endphp
                <li>
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $case->student->name ?? 'Unknown' }} ({{ $case->student->nim ?? 'Unknown' }})
                            </p>
                            <div class="ml-2 flex-shrink-0 flex space-x-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $roleLabel }}
                                </span>
                                {!! $statusBadge !!}
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                                <p class="flex items-center text-sm text-gray-500">
                                    {{ $case->metadata['report_title'] ?? 'Laporan KP' }}
                                </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                <p>
                                    @if($schedule)
                                        Jadwal: {{ $schedule->date->format('d/m/Y') }} {{ $schedule->start_time->format('H:i') }} - {{ $schedule->end_time->format('H:i') }} di {{ $schedule->room_or_link }}
                                    @else
                                        Belum dijadwalkan
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <a href="{{ route('lecturer.defenses.internship.assessment', $case->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                Buka Penilaian
                            </a>
                            <a href="{{ route('lecturer.defenses.internship.suggestion', $case->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                                Saran & Revisi
                            </a>
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    Tidak ada jadwal sidang untuk Anda saat ini.
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
