<div>
    @php
        $title = 'Sistem Form & Kalender Akademik Terpadu';
    @endphp
    <!-- Hero Section -->
    <section class="bg-teal-50 border-b border-teal-100 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-teal-100/30 skew-x-12 transform -mr-20 z-0 pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block py-1.5 px-3 rounded-md bg-teal-100 text-teal-800 text-xs font-bold tracking-widest uppercase mb-6 shadow-sm border border-teal-200">
                        Program Studi S1 Informatika &mdash; Universitas Pradita
                    </span>
                    <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                        Sistem Form & <span class="text-teal-600">Kalender Akademik</span> Terpadu
                    </h1>
                    <p class="text-lg text-slate-600 mb-8 max-w-xl leading-relaxed">
                        Sistem untuk pendaftaran, pengisian form kegiatan perkuliahan, dan pantau kalender akademik
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#form-akademik" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 transition">
                            Lihat Form Aktif
                        </a>
                        <a href="#kalender-akademik" class="inline-flex justify-center items-center px-6 py-3 border border-slate-300 shadow-sm text-base font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 transition">
                            Lihat Kalender Akademik
                        </a>
                    </div>
                </div>
                
                <!-- Hero Image / Icon -->
                <div class="flex justify-center relative mt-8 lg:mt-0">
                    <!-- Background decorative circle -->
                    <div class="absolute inset-0 bg-teal-100 rounded-full opacity-50 blur-3xl scale-125"></div>
                    
                    <svg class="w-64 h-64 lg:w-80 lg:h-80 text-teal-600 relative z-10 drop-shadow-xl transform hover:scale-105 transition duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        <!-- Add a checkmark for the integrated forms aspect -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 16l2 2 4-4" stroke="url(#gradient)"></path>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#0d9488" />
                                <stop offset="100%" stop-color="#14b8a6" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Stats Cards -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative z-20 -mt-10">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow border border-slate-200 p-4 flex flex-col items-center text-center">
                <span class="text-2xl font-bold text-teal-600 mb-1">{{ $stats['activeCount'] }}</span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Form Aktif</span>
            </div>
            <div class="bg-white rounded-xl shadow border border-slate-200 p-4 flex flex-col items-center text-center">
                <span class="text-xl font-bold text-slate-800 mb-1 truncate w-full" title="{{ $stats['activeSemester'] }}">{{ $stats['activeSemester'] }}</span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Semester Aktif</span>
            </div>
            <div class="bg-white rounded-xl shadow border border-slate-200 p-4 flex flex-col items-center text-center">
                <span class="text-xl font-bold text-slate-800 mb-1 truncate w-full" title="{{ $stats['currentMeetingWeek'] ? 'Pertemuan ' . $stats['currentMeetingWeek']->meeting_number : '-' }}">
                    {{ $stats['currentMeetingWeek'] ? 'Prt. ' . $stats['currentMeetingWeek']->meeting_number : '-' }}
                </span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Pekan Ini</span>
            </div>
            <div class="bg-white rounded-xl shadow border border-slate-200 p-4 flex flex-col items-center text-center">
                <span class="text-xl font-bold text-slate-800 mb-1 truncate w-full" title="{{ $stats['nextHoliday'] ? $stats['nextHoliday']->title : '-' }}">
                    {{ $stats['nextHoliday'] ? \Illuminate\Support\Str::limit($stats['nextHoliday']->title, 12) : '-' }}
                </span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Libur Terdekat</span>
            </div>
            <div class="bg-white rounded-xl shadow border border-slate-200 p-4 flex flex-col items-center text-center">
                <span class="text-xl font-bold text-slate-800 mb-1 truncate w-full" title="{{ $stats['nextExam'] ? $stats['nextExam']->title : '-' }}">
                    {{ $stats['nextExam'] ? \Illuminate\Support\Str::limit($stats['nextExam']->title, 12) : '-' }}
                </span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Ujian Terdekat</span>
            </div>
        </div>
    </section>

    <!-- CALENDAR SECTION -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" id="kalender-akademik" x-data="{ eventModalOpen: false, selectedEvent: null, dayModalOpen: false, selectedDayEvents: [] }">
        
        <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-slate-900 mb-2">Kalender Akademik</h2>
                <p class="text-slate-600">Jadwal resmi perkuliahan, ujian, dan kegiatan akademik.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <select wire:model.live="calendarSemester" class="w-full sm:w-64 py-2 px-3 border border-slate-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm font-medium">
                    @foreach($calendarSemesters as $cSem)
                        <option value="{{ $cSem->id }}">{{ $cSem->semester_name }}</option>
                    @endforeach
                </select>
                
                <div class="flex bg-slate-100 p-1 rounded-lg border border-slate-200 hidden md:flex">
                    <button wire:click="setMode('monthly')" class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ $calendarMode === 'monthly' ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">Kalender Bulanan</button>
                    <button wire:click="setMode('agenda')" class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors {{ $calendarMode === 'agenda' ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">Daftar Agenda</button>
                </div>
                <!-- Mobile only mode switcher forces agenda initially but allows month -->
                <div class="flex bg-slate-100 p-1 rounded-lg border border-slate-200 md:hidden w-full">
                    <button wire:click="setMode('monthly')" class="flex-1 px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ $calendarMode === 'monthly' ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">Bulanan</button>
                    <button wire:click="setMode('agenda')" class="flex-1 px-2 py-1.5 text-xs font-medium rounded-md transition-colors {{ $calendarMode === 'agenda' ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">Agenda</button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative">
            
            @if($calendarMode === 'monthly')
                <!-- MONTHLY VIEW CONTROLS -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900">
                        {{ \Carbon\Carbon::create($currentYear, $currentMonth, 1)->translatedFormat('F Y') }}
                    </h3>
                    <div class="flex gap-2">
                        <button wire:click="prevMonth" class="p-2 border border-slate-300 rounded bg-white text-slate-600 hover:bg-slate-50 transition" aria-label="Bulan Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button wire:click="jumpToToday" class="px-3 py-2 border border-slate-300 rounded bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">Hari Ini</button>
                        <button wire:click="nextMonth" class="p-2 border border-slate-300 rounded bg-white text-slate-600 hover:bg-slate-50 transition" aria-label="Bulan Selanjutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- MONTHLY GRID -->
                <div class="overflow-x-auto">
                    <div class="min-w-[800px] w-full border-b border-slate-200">
                        <!-- Days Header -->
                        <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-200">
                            @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                                <div class="py-2 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $day }}</div>
                            @endforeach
                        </div>
                        
                        <!-- Grid Cells -->
                        <div class="grid grid-cols-7 bg-slate-200 gap-px">
                            @foreach($grid as $cell)
                                @php
                                    $dateStr = $cell['date']->toDateString();
                                @endphp
                                <div class="min-h-[120px] bg-white p-2 flex flex-col relative transition hover:bg-slate-50 cursor-pointer"
                                     @click="selectedDayEvents = {{ json_encode($cell['events']) }}; dayModalOpen = true;">
                                    
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-sm font-medium {{ $cell['isCurrentMonth'] ? 'text-slate-700' : 'text-slate-400' }} {{ $cell['isToday'] ? 'bg-teal-600 text-white rounded-full w-6 h-6 flex items-center justify-center' : '' }}">
                                            {{ $cell['date']->format('j') }}
                                        </span>
                                        @if($cell['meetingWeek'])
                                            <span class="text-[9px] font-bold uppercase tracking-wider text-teal-600 bg-teal-50 px-1 rounded truncate max-w-[60px]" title="Pertemuan {{ $cell['meetingWeek']->meeting_number }}">
                                                Prt.{{ $cell['meetingWeek']->meeting_number }}
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-grow space-y-1 overflow-hidden mt-1">
                                        @foreach(array_slice($cell['events'], 0, 3) as $event)
                                            @php
                                                // Color Mapping based on spec
                                                $bgClass = 'bg-slate-100 text-slate-700';
                                                if($event['category_code'] == 'lecture') $bgClass = 'bg-blue-100 text-blue-800';
                                                elseif($event['category_code'] == 'holiday') $bgClass = 'bg-red-100 text-red-800';
                                                elseif($event['category_code'] == 'summative_exam' || $event['category_code'] == 'assessment_upload') $bgClass = 'bg-orange-100 text-orange-800';
                                                elseif($event['category_code'] == 'graduation' || $event['category_code'] == 'yudisium') $bgClass = 'bg-purple-100 text-purple-800';
                                            @endphp
                                            <div class="text-[10px] leading-tight px-1.5 py-1 rounded truncate {{ $bgClass }}" title="{{ $event['title'] }}" @click.stop="selectedEvent = {{ json_encode($event) }}; eventModalOpen = true;">
                                                <span class="font-semibold">{{ $event['is_tentative'] ? '(T) ' : '' }}</span>{{ $event['title'] }}
                                            </div>
                                        @endforeach
                                        @if(count($cell['events']) > 3)
                                            <div class="text-[10px] font-medium text-slate-500 px-1.5">+{{ count($cell['events']) - 3 }} agenda lainnya</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($calendarMode === 'agenda')
                <!-- AGENDA LIST VIEW -->
                <div class="p-6">
                    @if(count($agendaData) > 0)
                        <div class="space-y-10">
                            @foreach($agendaData as $monthKey => $monthGroups)
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900 border-b border-slate-200 pb-2 mb-4">
                                        {{ \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('F Y') }}
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        @foreach($monthGroups as $group)
                                            @php
                                                $firstSegment = (object) $group[0];
                                                $isGroup = count($group) > 1;
                                                $categoryText = $firstSegment->category_code; // Fallback
                                                // Map category properly if possible
                                                $catMap = [
                                                    'lecture' => 'Perkuliahan',
                                                    'academic_advising' => 'Bimbingan Akademik/KRS',
                                                    'orientation' => 'PKKMB',
                                                    'yudisium' => 'Yudisium',
                                                    'assessment_upload' => 'Upload Soal Sumatif',
                                                    'quiet_week' => 'Minggu Tenang/Input Nilai AF',
                                                    'summative_exam' => 'Pelaksanaan Sumatif',
                                                    'grade_entry' => 'Input Nilai Sumatif',
                                                    'khs' => 'KHS Online',
                                                    'graduation' => 'Wisuda',
                                                    'holiday' => 'Hari Libur'
                                                ];
                                                if(isset($catMap[$firstSegment->category_code])) {
                                                    $categoryText = $catMap[$firstSegment->category_code];
                                                }
                                            @endphp
                                            <div class="flex flex-col sm:flex-row gap-4 p-4 border border-slate-200 rounded-xl hover:shadow-md transition cursor-pointer bg-white" @click="selectedEvent = {{ json_encode($firstSegment) }}; eventModalOpen = true;">
                                                <!-- Date Block -->
                                                <div class="w-full sm:w-48 flex-shrink-0">
                                                    @if($isGroup)
                                                        <!-- Multi-segment Group Display -->
                                                        <div class="text-sm font-bold text-teal-700 flex flex-col gap-1">
                                                            @foreach($group as $seg)
                                                                @php $s = (object) $seg; @endphp
                                                                <span>{{ \Carbon\Carbon::parse($s->start_date)->format('d M') }} @if($s->end_date && $s->start_date !== $s->end_date) - {{ \Carbon\Carbon::parse($s->end_date)->format('d M') }} @endif</span>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <!-- Single Segment Display -->
                                                        <div class="text-sm font-bold text-teal-700">
                                                            {{ \Carbon\Carbon::parse($firstSegment->start_date)->format('d M Y') }}
                                                            @if($firstSegment->end_date && $firstSegment->start_date !== $firstSegment->end_date)
                                                                <br> s.d. {{ \Carbon\Carbon::parse($firstSegment->end_date)->format('d M Y') }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Info Block -->
                                                <div class="flex-grow">
                                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                                        <h4 class="text-base font-bold text-slate-900">{{ $firstSegment->title }}</h4>
                                                        @if($firstSegment->is_tentative)
                                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-orange-100 text-orange-800 border border-orange-200">Tentative</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500 mb-2">
                                                        <span class="px-2 py-1 rounded-full bg-slate-100 border border-slate-200">{{ $categoryText }}</span>
                                                    </div>
                                                    
                                                    @if($firstSegment->description)
                                                        <p class="text-sm text-slate-600 line-clamp-2">{{ $firstSegment->description }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-12 text-center text-slate-500">
                            Tidak ada agenda yang dipublikasikan pada semester ini.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- EVENT MODAL (AlpineJS) -->
        <div x-show="eventModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0">
            <!-- Backdrop -->
            <div x-show="eventModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="eventModalOpen = false"></div>
            
            <!-- Panel -->
            <div x-show="eventModalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 class="relative bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
                
                <div class="flex justify-between items-start p-6 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900 pr-8" x-text="selectedEvent ? selectedEvent.title : ''"></h3>
                    <button @click="eventModalOpen = false" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto">
                    <div class="space-y-4 text-sm">
                        
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div>
                                <span class="block font-semibold text-slate-800">Tanggal</span>
                                <span class="text-slate-600" x-text="selectedEvent ? (selectedEvent.start_date + (selectedEvent.end_date && selectedEvent.start_date !== selectedEvent.end_date ? ' s/d ' + selectedEvent.end_date : '')) : ''"></span>
                                <template x-if="selectedEvent && selectedEvent.is_tentative">
                                    <span class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-orange-100 text-orange-800 border border-orange-200">Tentative</span>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <div>
                                <span class="block font-semibold text-slate-800">Kategori</span>
                                <span class="text-slate-600 uppercase text-xs font-medium tracking-wide" x-text="selectedEvent ? selectedEvent.category_code.replace('_', ' ') : ''"></span>
                            </div>
                        </div>
                        
                        <template x-if="selectedEvent && selectedEvent.description">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-slate-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                <div>
                                    <span class="block font-semibold text-slate-800">Deskripsi</span>
                                    <span class="text-slate-600 leading-relaxed" x-text="selectedEvent.description"></span>
                                </div>
                            </div>
                        </template>

                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <span class="block font-semibold text-slate-800 mb-1 text-xs uppercase tracking-wide">Sumber Referensi</span>
                            <div class="bg-slate-50 rounded p-3 text-xs text-slate-600">
                                <div class="mb-1"><span class="font-medium">Dokumen:</span> <span x-text="selectedEvent ? selectedEvent.source_label : '-'"></span></div>
                                <div><span class="font-medium">Halaman:</span> <span x-text="selectedEvent ? selectedEvent.source_page : '-'"></span></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- DAY MODAL (AlpineJS) for viewing multiple events on mobile/grid -->
        <div x-show="dayModalOpen" style="display: none;" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 sm:p-0">
            <div x-show="dayModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="dayModalOpen = false"></div>
            
            <div x-show="dayModalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 class="relative bg-white rounded-t-2xl sm:rounded-2xl shadow-xl border border-slate-200 w-full max-w-sm overflow-hidden flex flex-col max-h-[80vh]">
                
                <div class="flex justify-between items-center p-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-base font-bold text-slate-900">Agenda Hari Ini</h3>
                    <button @click="dayModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-0 overflow-y-auto">
                    <template x-if="selectedDayEvents.length === 0">
                        <div class="p-6 text-center text-sm text-slate-500">Tidak ada agenda pada hari ini.</div>
                    </template>
                    <ul class="divide-y divide-slate-100">
                        <template x-for="ev in selectedDayEvents" :key="ev.id">
                            <li class="p-4 hover:bg-slate-50 cursor-pointer transition" @click="dayModalOpen = false; selectedEvent = ev; setTimeout(() => eventModalOpen = true, 300)">
                                <h4 class="text-sm font-bold text-slate-900 mb-1" x-text="ev.title"></h4>
                                <span class="text-xs text-slate-500" x-text="ev.category_code.replace('_', ' ').toUpperCase()"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

    </section>

    <!-- FORMS SECTION (Re-used existing UI but cleaned up) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-slate-200" id="form-akademik">
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-2">Form Akademik</h2>
            <p class="text-slate-600">Akses form untuk berbagai kegiatan akademik yang sedang berjalan.</p>
        </div>
        
        <!-- Filters -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <input wire:model.live="search" type="text" class="block w-full py-2 px-3 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm" placeholder="Cari form akademik...">
                </div>
                <div class="w-full md:w-48">
                    <select wire:model.live="filterActivityType" class="block w-full py-2 px-3 border border-slate-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full md:w-40">
                    <select wire:model.live="filterSemester" class="block w-full py-2 px-3 border border-slate-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                        <option value="">Semua Semester</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem }}">{{ $sem }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($activeForms as $form)
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:shadow-md transition">
                    <div class="p-5 flex-grow">
                        <div class="flex justify-between items-start mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                {{ $form->activityType->name ?? 'Umum' }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-widest uppercase bg-green-100 text-green-800 border border-green-200">
                                Aktif
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1 leading-snug">{{ $form->title }}</h3>
                        <p class="text-sm text-slate-600 line-clamp-2 mb-4">{{ $form->description }}</p>
                    </div>
                    <div class="bg-slate-50 px-5 py-4 border-t border-slate-100">
                        <a href="{{ route('student.login') }}" class="w-full text-center inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 transition">
                            Isi Form
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-12 text-center border-2 border-dashed border-slate-300 rounded-xl">
                    <p class="text-slate-500">Belum ada form yang aktif atau cocok dengan filter.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
