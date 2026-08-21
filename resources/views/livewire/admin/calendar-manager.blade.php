<div>
    @section('title', 'Manajemen Kalender Akademik')

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

    <div class="flex flex-col md:flex-row gap-6">
        
        <!-- Kolom Kiri: Daftar Semester -->
        <div class="w-full md:w-1/3">
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Semester Akademik</h3>
                    <button wire:click="openSemesterModal" class="text-sm bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded">
                        + Tambah
                    </button>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($semesters as $sem)
                        <li class="relative">
                            <button wire:click="selectSemester({{ $sem->id }})" class="w-full text-left p-4 hover:bg-blue-50 transition-colors {{ $activeSemesterId == $sem->id ? 'bg-blue-50 border-l-4 border-blue-600' : 'border-l-4 border-transparent' }}">
                                <div class="font-semibold text-gray-800 flex items-center gap-2">
                                    {{ $sem->semester_name }}
                                    @if($sem->source_document_code)
                                        <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    @if($sem->start_date) {{ $sem->start_date->format('d M Y') }} @endif 
                                    - 
                                    @if($sem->end_date) {{ $sem->end_date->format('d M Y') }} @endif
                                </div>
                            </button>
                            <div class="absolute right-4 top-4 flex space-x-2">
                                <button wire:click="editSemester({{ $sem->id }})" class="text-xs text-gray-500 hover:text-blue-600">
                                    {{ $sem->source_document_code ? 'Lihat' : 'Edit' }}
                                </button>
                                <button wire:click="openDuplicateSemesterModal({{ $sem->id }})" class="text-xs text-gray-500 hover:text-green-600">
                                    Duplikat
                                </button>
                                @if(!$sem->source_document_code)
                                    <button wire:click="confirmDeleteSemester({{ $sem->id }})" class="text-xs text-gray-500 hover:text-red-600">Hapus</button>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="p-4 text-center text-gray-500 text-sm">Belum ada data semester.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Kolom Kanan: Daftar Agenda untuk Semester Terpilih -->
        <div class="w-full md:w-2/3">
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-200 min-h-[400px]">
                @if($activeSemesterId)
                    @php $activeSem = $semesters->firstWhere('id', $activeSemesterId); @endphp
                    <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Agenda: {{ $activeSem->semester_name ?? '' }}</h3>
                        <button wire:click="openEventModal" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-1.5 px-3 rounded text-sm shadow">
                            + Tambah Agenda
                        </button>
                    </div>
                    
                    <div class="p-0 overflow-x-auto">
                        <!-- Bulk Actions and CSV -->
                        <div class="p-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                <select wire:model="bulkAction" class="text-sm border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Aksi Massal --</option>
                                    <option value="publish">Jadikan Publik</option>
                                    <option value="unpublish">Jadikan Draft</option>
                                    <option value="duplicate">Duplikat</option>
                                    <option value="delete">Hapus</option>
                                </select>
                                <button wire:click="executeBulkAction(bulkAction)" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm py-1 px-3 rounded shadow">Terapkan</button>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="downloadCsvTemplate" class="text-xs text-blue-600 hover:underline">Download Template</button>
                                <input type="file" wire:model="csvFile" id="csvUpload" class="hidden" accept=".csv">
                                <label for="csvUpload" class="cursor-pointer bg-green-600 hover:bg-green-700 text-white text-sm py-1 px-3 rounded shadow">Import CSV</label>
                                <button wire:click="processCsvUpload" class="hidden" id="processCsvBtn"></button>
                                <button wire:click="exportEventsCsv" class="bg-blue-600 hover:bg-blue-700 text-white text-sm py-1 px-3 rounded shadow">Export CSV</button>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-10">
                                        <input type="checkbox" wire:model.live="selectAllEvents" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agenda</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Publik</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($events as $event)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <input type="checkbox" wire:model="selectedEvents" value="{{ $event->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                            @if($event->start_date) {{ $event->start_date->format('d M Y') }} @endif
                                            @if($event->end_date && $event->end_date->format('Y-m-d') != $event->start_date->format('Y-m-d'))
                                                <br><span class="text-xs text-gray-400">s/d {{ $event->end_date->format('d M Y') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                                            <div class="flex items-center gap-1">
                                                @if($event->is_source_locked)
                                                    <svg class="w-3 h-3 text-red-500 shrink-0" title="Data Resmi" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                                                @endif
                                                {{ $event->title }}
                                            </div>
                                            
                                            <div class="flex gap-1 mt-1">
                                                @if($event->form_id)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">
                                                        Form
                                                    </span>
                                                @endif
                                                @if($event->is_tentative)
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-orange-100 text-orange-800">
                                                        Tentative
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                            {{ ucfirst(str_replace('_', ' ', $event->category_code)) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            @if($event->is_public)
                                                <span class="text-green-600">Ya</span>
                                            @else
                                                <span class="text-red-500">Tidak</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium flex gap-2">
                                            <button wire:click="editEvent({{ $event->id }})" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $event->is_source_locked ? 'Lihat' : 'Edit' }}
                                            </button>
                                            <button wire:click="duplicateEvent({{ $event->id }})" class="text-green-600 hover:text-green-900">Duplikat</button>
                                            @if(!$event->is_source_locked)
                                                <button wire:click="confirmDeleteEvent({{ $event->id }})" class="text-red-600 hover:text-red-900">Hapus</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada agenda di semester ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full p-8 text-gray-400 text-center flex-col">
                        <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p>Pilih atau buat semester akademik di panel kiri untuk mengelola agenda.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Semester -->
    @if($isSemesterModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isSemesterModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="storeSemester">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex justify-between items-center">
                            {{ $semesterId ? ($isSemesterLocked ? 'Detail Semester Resmi' : 'Edit Semester') : 'Tambah Semester' }}
                            @if($isSemesterLocked)
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-bold uppercase">Terkunci</span>
                            @endif
                        </h3>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Semester</label>
                            <input type="text" wire:model="semesterName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isSemesterLocked ? 'disabled' : '' }}>
                            @error('semesterName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status Publikasi</label>
                                <select wire:model="semester_publication_status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-6">
                                <input id="is_active" type="checkbox" wire:model="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $isSemesterLocked ? 'disabled' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900 font-bold">
                                    Semester Aktif
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Internal (Opsional)</label>
                            <textarea wire:model="notes" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isSemesterLocked ? 'disabled' : '' }}></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-2">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                                <input type="date" wire:model="semesterStartDate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isSemesterLocked ? 'disabled' : '' }}>
                                @error('semesterStartDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai</label>
                                <input type="date" wire:model="semesterEndDate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isSemesterLocked ? 'disabled' : '' }}>
                                @error('semesterEndDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if(!$isSemesterLocked)
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan</button>
                        @endif
                        <button type="button" wire:click="$set('isSemesterModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Event -->
    @if($isEventModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isEventModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full max-h-[90vh] overflow-y-auto">
                <form wire:submit.prevent="storeEvent">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2 flex justify-between items-center">
                            {{ $eventId ? ($isEventLocked ? 'Detail Agenda Resmi' : 'Edit Agenda') : 'Tambah Agenda' }}
                            @if($isEventLocked)
                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-bold uppercase">Terkunci</span>
                            @endif
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Judul Agenda</label>
                                <input type="text" wire:model="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}>
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori Agenda</label>
                                <select wire:model="category_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}>
                                    <option value="lecture">Perkuliahan</option>
                                    <option value="academic_advising">Bimbingan Akademik / KRS</option>
                                    <option value="orientation">PKKMB</option>
                                    <option value="yudisium">Yudisium</option>
                                    <option value="assessment_upload">Upload Soal Sumatif</option>
                                    <option value="quiet_week">Minggu Tenang / Input Nilai AF</option>
                                    <option value="summative_exam">Pelaksanaan Sumatif</option>
                                    <option value="grade_entry">Input Nilai Sumatif</option>
                                    <option value="khs">KHS Online</option>
                                    <option value="graduation">Wisuda</option>
                                    <option value="holiday">Libur Akademik</option>
                                    <option value="other">Lainnya</option>
                                </select>
                                @error('category_code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Semester</label>
                                <select wire:model="academic_calendar_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100" disabled>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->semester_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 border-t border-gray-100 pt-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai</label>
                                <input type="date" wire:model.live="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}>
                                @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Selesai (Opsional)</label>
                                <input type="date" wire:model="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}>
                                @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-4 border-t border-gray-100 pt-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Form Terkait (Opsional)</label>
                            <select wire:model.live="form_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-white focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}>
                                <option value="">-- Tidak Terhubung --</option>
                                @foreach($forms as $form)
                                    <option value="{{ $form->id }}">{{ $form->title }} ({{ $form->semester }})</option>
                                @endforeach
                            </select>
                            @if($form_id && empty($eventId) && !$isEventLocked)
                                <p class="text-xs text-blue-600 mt-1">Sistem otomatis mengisi tanggal mulai dan selesai dari data Form jika tanggal belum diisi.</p>
                            @endif
                            @error('form_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi Keterangan (Opsional)</label>
                            <textarea wire:model="description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">URL Eksternal (Opsional)</label>
                            <input type="text" wire:model="external_url" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }} placeholder="https://...">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Internal (Opsional)</label>
                            <textarea wire:model="internal_notes" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-blue-500 focus:border-blue-500" {{ $isEventLocked ? 'disabled' : '' }}></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-2">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status Publikasi</label>
                                <select wire:model="event_publication_status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" {{ $isEventLocked ? 'disabled' : '' }}>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="flex items-center mt-6">
                                <input id="is_public" type="checkbox" wire:model="is_public" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $isEventLocked ? 'disabled' : '' }}>
                                <label for="is_public" class="ml-2 block text-sm text-gray-900 font-bold">
                                    Tampilkan Publik
                                </label>
                            </div>
                            <div class="flex items-center mt-6">
                                <input id="is_tentative" type="checkbox" wire:model="is_tentative" class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded" {{ $isEventLocked ? 'disabled' : '' }}>
                                <label for="is_tentative" class="ml-2 block text-sm text-gray-900 font-bold">
                                    Jadwal Tentative
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t">
                        @if(!$isEventLocked)
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Simpan Agenda</button>
                        @endif
                        <button type="button" wire:click="$set('isEventModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Confirm Delete Semester -->
    @if($isDeleteSemesterModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isDeleteSemesterModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2 text-red-600">Konfirmasi Hapus Semester</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Terdapat <strong>{{ $deletingSemesterHasEvents ? 'beberapa' : '0' }}</strong> agenda pada semester ini. Apa yang ingin Anda lakukan?
                    </p>
                    <div class="space-y-3">
                        <button wire:click="executeDeleteSemester('archive')" class="w-full flex justify-center items-center px-4 py-2 border border-yellow-300 shadow-sm text-sm font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                            Arsipkan Semester Saja
                        </button>
                        @if(auth()->user()->role === 'admin_forta')
                        <button wire:click="executeDeleteSemester('force_delete')" class="w-full flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                            Hapus Permanen beserta Seluruh Agenda
                        </button>
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="$set('isDeleteSemesterModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Confirm Delete Event -->
    @if($isDeleteEventModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isDeleteEventModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hapus Agenda</h3>
                    <p class="text-sm text-gray-500">Anda yakin ingin menghapus agenda ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="executeDeleteEvent" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Hapus</button>
                    <button type="button" wire:click="$set('isDeleteEventModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Duplicate Semester -->
    @if($isDuplicateSemesterModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isDuplicateSemesterModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="executeDuplicateSemester">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Duplikat Semester</h3>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Semester Baru</label>
                            <input type="text" wire:model="dupSemesterName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('dupSemesterName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Akademik</label>
                            <input type="text" wire:model="dupAcademicYear" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai Baru</label>
                            <input type="date" wire:model="dupStartDate" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            @error('dupStartDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <p class="text-xs text-gray-500 mt-1">Semua agenda akan digeser secara proporsional dari tanggal mulai baru ini. Semua agenda yang diduplikasi akan berstatus draft.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Duplikat</button>
                        <button type="button" wire:click="$set('isDuplicateSemesterModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal CSV Preview -->
    @if($isCsvPreviewModalOpen)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 opacity-75 transition-opacity" wire:click="$set('isCsvPreviewModalOpen', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[80vh] overflow-y-auto">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Preview Import CSV</h3>
                    
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Mode Import jika Duplikat / Error</label>
                        <select wire:model="csvImportMode" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-white">
                            <option value="skip">Lewati (Abaikan data duplikat/error)</option>
                            <option value="update">Update (Timpa data yang sudah ada)</option>
                            <option value="abort">Batalkan (Berhenti jika ada error)</option>
                        </select>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Semester</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Agenda</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tgl Mulai</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($csvPreviewData as $row)
                                    <tr class="{{ $row['_status'] == 'error' ? 'bg-red-50' : '' }}">
                                        <td class="px-2 py-2 whitespace-nowrap text-xs">
                                            @if($row['_status'] == 'error')
                                                <span class="text-red-600 font-bold" title="{{ $row['_message'] }}">Error</span>
                                            @else
                                                <span class="text-green-600 font-bold">Valid</span>
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-xs">{{ $row['semester_code'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-xs">{{ $row['title'] ?? '-' }}</td>
                                        <td class="px-2 py-2 text-xs">{{ $row['start_date'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="confirmCsvImport" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">Mulai Import</button>
                    <button type="button" wire:click="$set('isCsvPreviewModalOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
