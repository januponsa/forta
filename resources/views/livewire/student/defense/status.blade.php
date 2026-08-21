<div>
    @section('title', 'Status Sidang Magang/KP')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Informasi Pendaftaran Sidang
        </h3>
    </div>
    
    <div class="p-6">
        @if(!$case)
            <div class="text-center py-10 bg-white shadow rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Data Sidang Belum Ditemukan</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum mendaftar atau pendaftaran Anda belum diverifikasi oleh admin.</p>
            </div>
        @else
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            {{ $case->metadata['report_title'] ?? 'Laporan Magang/KP' }}
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            Perusahaan: {{ $case->metadata['company_name'] ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Status: {{ str_replace('_', ' ', Str::title($case->status)) }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Jadwal Sidang</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                @if($case->latestSchedule)
                                    <div class="font-semibold">{{ $case->latestSchedule->date->format('l, d F Y') }}</div>
                                    <div>Pukul: {{ $case->latestSchedule->start_time->format('H:i') }} - {{ $case->latestSchedule->end_time->format('H:i') }}</div>
                                    <div>Ruang/Link: {{ $case->latestSchedule->room_or_link }}</div>
                                @else
                                    <span class="text-gray-500 italic">Belum dijadwalkan oleh admin.</span>
                                @endif
                            </dd>
                        </div>
                        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Tim Penilai</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                    @php
                                        $supervisor = $case->assignments->where('role', 'supervisor')->first();
                                        $examiner = $case->assignments->where('role', 'examiner')->first();
                                    @endphp
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <span class="ml-2 flex-1 w-0 truncate">Pembimbing: {{ $supervisor->lecturer->name ?? 'Belum ditentukan' }}</span>
                                        </div>
                                    </li>
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <span class="ml-2 flex-1 w-0 truncate">Penguji: {{ $examiner->lecturer->name ?? 'Belum ditentukan' }}</span>
                                        </div>
                                    </li>
                                    <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <span class="ml-2 flex-1 w-0 truncate">Mentor Lapangan: {{ $case->metadata['mentor_name'] ?? '-' }}</span>
                                        </div>
                                    </li>
                                </ul>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
            
            <div class="flex justify-center space-x-4">
                <a href="{{ route('student.defenses.internship.revision', $case->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    Lihat Saran & Revisi
                </a>
                <a href="{{ route('student.defenses.internship.result', $case->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                    Lihat Hasil Akhir
                </a>
            </div>
        @endif
    </div>
</div>
