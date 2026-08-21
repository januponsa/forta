<div>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Detail Kampanye Email</h2>
        <div>
            <a href="{{ route('admin.email-blast.history') }}" class="text-gray-600 hover:underline">Kembali ke Riwayat</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="col-span-2 bg-white rounded shadow-sm p-6">
            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Informasi Utama</h3>
            <table class="w-full text-sm">
                <tbody>
                    <tr><td class="py-2 text-gray-500 w-1/4">Subject</td><td class="py-2 font-bold">{{ $campaign->subject }}</td></tr>
                    <tr><td class="py-2 text-gray-500">Dibuat Oleh</td><td class="py-2">{{ $campaign->createdBy->name ?? '-' }}</td></tr>
                    <tr><td class="py-2 text-gray-500">Tanggal Dibuat</td><td class="py-2">{{ $campaign->created_at->format('d/m/Y H:i:s') }}</td></tr>
                    <tr><td class="py-2 text-gray-500">Tanggal Dikirim</td><td class="py-2">{{ $campaign->sent_at ? $campaign->sent_at->format('d/m/Y H:i:s') : '-' }}</td></tr>
                    <tr><td class="py-2 text-gray-500">Mode Pengiriman</td><td class="py-2 uppercase">{{ $campaign->delivery_mode }}</td></tr>
                    <tr><td class="py-2 text-gray-500">Target</td><td class="py-2">{{ $campaign->target_description }}</td></tr>
                    <tr>
                        <td class="py-2 text-gray-500">Lampiran</td>
                        <td class="py-2">
                            @if($campaign->attachments->isEmpty())
                                -
                            @else
                                <ul class="list-disc pl-5">
                                    @foreach($campaign->attachments as $att)
                                        <li><a href="#" class="text-blue-600 hover:underline">{{ $att->file_name }}</a> ({{ round($att->file_size/1024) }} KB)</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <h3 class="text-lg font-semibold border-b pb-2 mt-6 mb-4">Isi Email</h3>
            <div class="border rounded p-4 bg-gray-50 prose max-w-none text-sm">
                {!! nl2br(e($campaign->body_html)) !!}
            </div>
        </div>
        
        <div class="col-span-1 bg-white rounded shadow-sm p-6">
            <h3 class="text-lg font-semibold border-b pb-2 mb-4">Statistik Pengiriman</h3>
            <div class="mb-4">
                <span class="text-sm text-gray-500 block">Status Kampanye</span>
                <span class="text-lg font-bold uppercase {{ $campaign->status === 'completed' ? 'text-green-600' : ($campaign->status === 'failed' ? 'text-red-600' : 'text-blue-600') }}">
                    {{ $campaign->status }}
                </span>
            </div>
            <div class="mb-4">
                <span class="text-sm text-gray-500 block">Total Penerima</span>
                <span class="text-2xl font-bold">{{ $campaign->total_recipients }}</span>
            </div>
            
            @php
                $sentCount = $campaign->recipients()->where('status', 'sent')->count();
                $failedCount = $campaign->recipients()->where('status', 'failed')->count();
                $pendingCount = $campaign->recipients()->where('status', 'pending')->count();
            @endphp
            
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 p-3 rounded border border-green-200">
                    <span class="text-xs text-green-600 block">Berhasil</span>
                    <span class="text-xl font-bold text-green-700">{{ $sentCount }}</span>
                </div>
                <div class="bg-red-50 p-3 rounded border border-red-200">
                    <span class="text-xs text-red-600 block">Gagal</span>
                    <span class="text-xl font-bold text-red-700">{{ $failedCount }}</span>
                </div>
                <div class="bg-yellow-50 p-3 rounded border border-yellow-200 col-span-2">
                    <span class="text-xs text-yellow-600 block">Menunggu (Antrean)</span>
                    <span class="text-xl font-bold text-yellow-700">{{ $pendingCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow-sm p-6">
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Daftar Penerima</h3>
        
        <div class="flex space-x-4 mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Nama/NIM/Email..." class="shadow border rounded w-1/3 py-2 px-3 text-sm">
            <select wire:model.live="filterStatus" class="shadow border rounded py-2 px-3 text-sm">
                <option value="">Semua Status</option>
                <option value="sent">Berhasil</option>
                <option value="failed">Gagal</option>
                <option value="pending">Menunggu</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-700">NIM</th>
                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-700">Nama</th>
                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-700">Email</th>
                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-700">Angkatan</th>
                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-700">Status</th>
                        <th class="py-2 px-4 text-left text-xs font-semibold text-gray-700">Keterangan Error</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recipients as $recipient)
                        <tr class="hover:bg-gray-50 text-sm">
                            <td class="py-2 px-4 text-gray-800">{{ $recipient->nim }}</td>
                            <td class="py-2 px-4 text-gray-800">{{ $recipient->name }}</td>
                            <td class="py-2 px-4 text-gray-800">{{ $recipient->email }}</td>
                            <td class="py-2 px-4 text-gray-800">{{ $recipient->angkatan }}</td>
                            <td class="py-2 px-4">
                                @if($recipient->status === 'sent')
                                    <span class="text-green-600 font-semibold">Berhasil</span>
                                @elseif($recipient->status === 'failed')
                                    <span class="text-red-600 font-semibold">Gagal</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Menunggu</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 text-red-500 text-xs truncate max-w-xs" title="{{ $recipient->error_message }}">
                                {{ $recipient->error_message ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-500">Tidak ada penerima yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $recipients->links() }}
        </div>
    </div>
</div>
