<div>
    @section('title', 'Manajemen Sidang - Penjadwalan & Penugasan')
    
    <div class="px-4 py-5 sm:px-6 bg-white border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Penjadwalan & Penugasan Sidang
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
        @if (session()->has('message'))
            <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                <p class="text-sm text-green-700">{{ session('message') }}</p>
            </div>
        @endif
        
        @if (session()->has('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal & Ruang</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cases as $case)
                        @php
                            $schedule = $case->latestSchedule;
                            $supervisor = $case->assignments->where('role', 'supervisor')->first();
                            $examiner = $case->assignments->where('role', 'examiner')->first();
                        @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $case->student->name ?? 'Unknown' }}</div>
                            <div class="text-sm text-gray-500">{{ $case->student->nim ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-400">{{ $case->metadata['report_title'] ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($schedule)
                                <div class="text-sm text-gray-900">{{ $schedule->date ? $schedule->date->format('d/m/Y') : '-' }}</div>
                                <div class="text-sm text-gray-500">{{ $schedule->start_time ? $schedule->start_time->format('H:i') : '-' }} - {{ $schedule->end_time ? $schedule->end_time->format('H:i') : '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $schedule->room_or_link }}</div>
                            @else
                                <span class="text-sm text-red-500">Belum dijadwalkan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Pembimbing: {{ $supervisor->lecturer->name ?? '-' }}</div>
                            <div class="text-sm text-gray-500">Penguji: {{ $examiner->lecturer->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $case->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="openModal({{ $case->id }})" class="text-indigo-600 hover:text-indigo-900">Jadwalkan</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Belum ada peserta sidang.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
        <!-- Modal Jadwal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xl mx-auto flex flex-col max-h-full">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Penjadwalan: {{ $studentName }}
                </h3>
                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form wire:submit.prevent="saveSchedule" class="flex-1 overflow-y-auto">
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Sidang</label>
                        <input type="date" wire:model="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex space-x-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                            <input type="time" wire:model="start_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('start_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                            <input type="time" wire:model="end_time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @error('end_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ruang / Link Daring</label>
                        <input type="text" wire:model="room_or_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('room_or_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <hr class="my-4 border-gray-200">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dosen Pembimbing</label>
                        <select wire:model="supervisor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Pilih Pembimbing --</option>
                            @foreach($lecturers as $lec)
                                <option value="{{ $lec->id }}">{{ $lec->name }}</option>
                            @endforeach
                        </select>
                        @error('supervisor_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Dosen Penguji / Ketua</label>
                        <select wire:model="examiner_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">-- Pilih Penguji --</option>
                            @foreach($lecturers as $lec)
                                <option value="{{ $lec->id }}">{{ $lec->name }}</option>
                            @endforeach
                        </select>
                        @error('examiner_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
