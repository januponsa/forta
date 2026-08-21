<div>
    @section('title', 'Manajemen Sidang - Peserta Sidang')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Peserta Sidang Magang/KP
        </h3>
        <div>
            <select wire:model.live="semesterFilter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                <option value="">Semua Semester</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem }}">{{ $sem }}</option>
                @endforeach
            </select>
            <button wire:click="syncParticipants" class="ml-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                Tarik Pendaftar Baru
            </button>
        </div>
    </div>
    
    <div class="p-4">
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @forelse($cases as $case)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ $case->student->name ?? 'Unknown' }} ({{ $case->student->nim ?? 'Unknown' }})
                            </p>
                            <div class="ml-2 flex-shrink-0 flex">
                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $case->status }}
                                </p>
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
                                    Semester: {{ $case->semester }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-400">
                            Perusahaan: {{ $case->metadata['company_name'] ?? '-' }} | Mentor: {{ $case->metadata['mentor_name'] ?? '-' }}
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-4 py-8 text-center text-gray-500">
                    Belum ada peserta sidang. Klik "Tarik Pendaftar Baru" untuk mensinkronisasi data dari form pendaftaran.
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
