<div>
    @section('title', 'Admin Dashboard & Analytics')

    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Sistem Overview & Statistik</h2>
            <p class="text-xs text-gray-500">Pantau data registrasi mahasiswa, pengajuan formulir, dan audit log sistem.</p>
        </div>
    </div>

    {{-- Metrics grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-500 hover:shadow-lg transition">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Mahasiswa Aktif</h3>
            <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $stats['total_students'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-green-500 hover:shadow-lg transition">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Formulir Aktif</h3>
            <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $stats['active_forms'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-yellow-500 hover:shadow-lg transition">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menunggu Review</h3>
            <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $stats['pending_submissions'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-500 hover:shadow-lg transition">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Disetujui</h3>
            <p class="mt-2 text-3xl font-extrabold text-gray-900">{{ $stats['approved_submissions'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left: Form Statistics and Recent Submissions --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Form statistics report --}}
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Laporan Statistik Pengajuan per Form</h3>
                    <span class="text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded font-semibold">{{ $formStats->count() }} Formulir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Formulir</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider text-yellow-600 font-bold">Pending</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider text-green-600 font-bold">Approved</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider text-red-600 font-bold">Rejected</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($formStats as $fs)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                        {{ $fs->title }}
                                        <span class="ml-1 bg-slate-100 text-slate-700 text-xxs px-1.5 py-0.5 rounded font-mono">v{{ $fs->version }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold text-gray-900">{{ $fs->submissions_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-yellow-600 bg-yellow-50/50">{{ $fs->pending_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600 bg-green-50/50">{{ $fs->approved_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-red-600 bg-red-50/50">{{ $fs->rejected_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-xs text-gray-400 italic">Belum ada statistik form.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Submissions --}}
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Pengajuan Terbaru</h3>
                    <a href="{{ route('admin.submissions') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Formulir</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($recentSubmissions as $sub)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                        {{ $sub->name }}
                                        <div class="text-xxs text-gray-400 font-mono">{{ $sub->nim }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sub->form->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-mono">{{ $sub->submitted_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-0.5 inline-flex text-xxs leading-5 font-bold rounded-full 
                                            {{ $sub->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                              ($sub->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                              ($sub->status == 'revision' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                            {{ strtoupper($sub->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-xs text-gray-400 italic">Belum ada pengajuan masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Right: Audit Trail Log --}}
        <div>
            <div class="bg-white shadow rounded-lg overflow-hidden border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-800">Aktivitas Audit Log Terbaru</h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($recentAuditLogs as $log)
                        <div class="p-3 border rounded bg-gray-50 hover:bg-white hover:shadow-sm transition text-xs space-y-1.5">
                            <div class="flex justify-between items-center text-xxs">
                                <span class="font-bold text-indigo-700 capitalize">{{ optional($log->actor)->name ?? 'System' }} ({{ $log->actor_role }})</span>
                                <span class="text-gray-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-gray-700 font-semibold">
                                Action: <span class="bg-indigo-50 text-indigo-700 px-1 py-0.2 rounded font-mono">{{ $log->action }}</span>
                            </div>
                            <div class="text-xxs text-gray-400 flex justify-between">
                                <span>Target: {{ $log->target_type }} #{{ $log->target_id }}</span>
                                <span class="font-mono">{{ $log->ip_address }}</span>
                            </div>
                            @if ($log->freed_bytes)
                                <div class="text-xxs text-emerald-600 font-bold">
                                    Freed storage: {{ round($log->freed_bytes / 1024, 2) }} KB
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 italic text-center py-6">Belum ada aktivitas audit log.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
