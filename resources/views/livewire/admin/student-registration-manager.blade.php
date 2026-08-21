<div>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Pendaftaran Mahasiswa ✨</h1>
                <p class="text-slate-500">Kelola permintaan pendaftaran akun dari mahasiswa baru.</p>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-4 px-4 py-2 bg-green-50 border border-green-200 text-green-700 rounded-md">
                {{ session('message') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-sm border border-slate-200 relative">
            <div class="overflow-x-auto">
                <table class="table-auto w-full divide-y divide-slate-200">
                    <thead class="text-xs uppercase text-slate-500 bg-slate-50 border-t border-slate-200">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Nama / NIM</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Email / Angkatan</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Tgl Pengajuan</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Konflik</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Status</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Aksi</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse($requests as $req)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 shrink-0 mr-2 sm:mr-3">
                                            @if($req->google_avatar)
                                                <img class="rounded-full" src="{{ $req->google_avatar }}" width="40" height="40" alt="{{ $req->name }}" />
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold">
                                                    {{ substr($req->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-medium text-slate-800">{{ $req->name }}</div>
                                            <div class="text-slate-500">{{ $req->nim }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div>{{ $req->google_email }}</div>
                                    <div class="text-slate-500 font-medium">Angkatan {{ $req->angkatan }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div>{{ \Carbon\Carbon::parse($req->requested_at)->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    @if($req->conflict_type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $req->conflict_type }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    @if($req->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                                    @elseif($req->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                    @if($req->status === 'pending')
                                        <a href="{{ route('admin.registrations.show', $req->id) }}" class="text-indigo-500 hover:text-indigo-600 font-medium text-sm">
                                            Review
                                        </a>
                                    @else
                                        <a href="{{ route('admin.registrations.show', $req->id) }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">
                                            Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-2 first:pl-5 last:pr-5 py-8 text-center text-slate-500">
                                    Belum ada pengajuan pendaftaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
