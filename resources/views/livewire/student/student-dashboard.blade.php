<div>
    @section('title', 'Dashboard Mahasiswa')

    <!-- Hero Section -->
    <div class="bg-[#0f172a] rounded-xl shadow-lg p-8 mb-8 text-white relative overflow-hidden">
        <div class="relative z-10">
            <h2 class="text-3xl font-bold mb-2">Selamat Datang di FORTA, {{ Auth::guard('student')->user()->name }}</h2>
            <p class="text-slate-300 text-lg max-w-2xl">Kelola pengajuan form akademik Anda dengan mudah dan pantau statusnya secara real-time.</p>
        </div>
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-[#0d9488] rounded-full opacity-20 blur-3xl"></div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
            {{ session('message') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Perlu Diisi</p>
                <p class="text-3xl font-bold text-[#0f172a]">{{ $totalBelumDiisi }}</p>
            </div>
            <div class="w-12 h-12 bg-teal-50 rounded-full flex items-center justify-center text-[#0d9488]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Sudah Dikirim</p>
                <p class="text-3xl font-bold text-[#0f172a]">{{ $totalSudahDikirim }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 mb-1">Perlu Revisi</p>
                <p class="text-3xl font-bold text-orange-600">{{ $totalPerluRevisi }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center text-orange-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Alpine Tabs -->
    <div x-data="{ tab: 'active' }">
        <div class="flex space-x-1 border-b border-slate-200 mb-6 overflow-x-auto">
            <button @click="tab = 'active'" :class="{'border-b-2 border-[#0d9488] text-[#0d9488]': tab === 'active', 'text-slate-500 hover:text-slate-700': tab !== 'active'}" class="px-6 py-3 text-sm font-medium focus:outline-none transition-colors whitespace-nowrap">
                Form Aktif ({{ $activeForms->count() }})
            </button>
            <button @click="tab = 'upcoming'" :class="{'border-b-2 border-[#0d9488] text-[#0d9488]': tab === 'upcoming', 'text-slate-500 hover:text-slate-700': tab !== 'upcoming'}" class="px-6 py-3 text-sm font-medium focus:outline-none transition-colors whitespace-nowrap">
                Akan Datang ({{ $upcomingForms->count() }})
            </button>
            <button @click="tab = 'closed'" :class="{'border-b-2 border-[#0d9488] text-[#0d9488]': tab === 'closed', 'text-slate-500 hover:text-slate-700': tab !== 'closed'}" class="px-6 py-3 text-sm font-medium focus:outline-none transition-colors whitespace-nowrap">
                Ditutup ({{ $closedForms->count() }})
            </button>
            <button @click="tab = 'history'" :class="{'border-b-2 border-[#0d9488] text-[#0d9488]': tab === 'history', 'text-slate-500 hover:text-slate-700': tab !== 'history'}" class="px-6 py-3 text-sm font-medium focus:outline-none transition-colors whitespace-nowrap">
                Riwayat Pengajuan
            </button>
        </div>

        <!-- Active Forms -->
        <div x-show="tab === 'active'" class="space-y-4">
            @forelse($activeForms as $form)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs font-semibold rounded">{{ $form->activityType->name ?? 'Umum' }}</span>
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 text-xs font-medium rounded">{{ $form->semester }} - {{ $form->phase }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-[#0f172a] mb-2">{{ $form->title }}</h3>
                        <p class="text-slate-600 text-sm mb-4 md:mb-0">{{ Str::limit($form->description, 120) }}</p>
                    </div>
                    <div class="flex flex-col items-start md:items-end w-full md:w-auto mt-4 md:mt-0 shrink-0">
                        <div class="text-sm text-slate-500 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Deadline: {{ $form->close_at->format('d M Y, H:i') }}
                        </div>
                        @if(in_array($form->id, $submittedFormIds))
                            <span class="px-6 py-2 bg-green-100 text-green-800 font-medium rounded-md shadow-sm w-full md:w-auto text-center cursor-not-allowed">
                                Sudah Dikirim
                            </span>
                        @else
                            <a wire:navigate href="{{ route('student.forms.show', $form->slug) }}" class="px-6 py-2 bg-[#0d9488] hover:bg-teal-700 text-white font-medium rounded-md shadow-sm transition w-full md:w-auto text-center">
                                Isi Form
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-xl border border-slate-200">
                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <h3 class="mt-2 text-sm font-medium text-slate-900">Tidak ada form aktif</h3>
                <p class="mt-1 text-sm text-slate-500">Saat ini tidak ada form yang perlu diisi.</p>
            </div>
            @endforelse
        </div>

        <!-- Upcoming Forms -->
        <div x-show="tab === 'upcoming'" class="space-y-4" style="display: none;">
            @forelse($upcomingForms as $form)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 opacity-75">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 bg-teal-100 text-teal-800 text-xs font-semibold rounded">{{ $form->activityType->name ?? 'Umum' }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-[#0f172a] mb-2">{{ $form->title }}</h3>
                    </div>
                    <div class="flex flex-col items-start md:items-end w-full md:w-auto mt-4 md:mt-0 shrink-0">
                        <div class="text-sm text-slate-500 flex items-center">
                            <svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Akan Buka: {{ $form->open_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-xl border border-slate-200">
                <p class="text-sm text-slate-500">Tidak ada form yang dijadwalkan.</p>
            </div>
            @endforelse
        </div>

        <!-- Closed Forms -->
        <div x-show="tab === 'closed'" class="space-y-4" style="display: none;">
            @forelse($closedForms as $form)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 opacity-60 grayscale">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 bg-slate-200 text-slate-600 text-xs font-semibold rounded">{{ $form->activityType->name ?? 'Umum' }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700 mb-2">{{ $form->title }}</h3>
                    </div>
                    <div class="flex flex-col items-start md:items-end w-full md:w-auto mt-4 md:mt-0 shrink-0">
                        <div class="text-sm text-red-500 font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Ditutup pada: {{ $form->close_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 bg-white rounded-xl border border-slate-200">
                <p class="text-sm text-slate-500">Belum ada form yang ditutup.</p>
            </div>
            @endforelse
        </div>

        <!-- History -->
        <div x-show="tab === 'history'" class="space-y-4" style="display: none;">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Form</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Dikirim</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($mySubmissions as $submission)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ $submission->form->title ?? 'Form Dihapus' }}</div>
                                <div class="text-xs text-slate-500">{{ $submission->form->activityType->name ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($submission->status === 'submitted')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Menunggu Review</span>
                                @elseif($submission->status === 'approved')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                @elseif($submission->status === 'revision')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Revisi</span>
                                @elseif($submission->status === 'rejected')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800">{{ ucfirst($submission->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-500 text-sm">
                                Belum ada riwayat pengajuan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
