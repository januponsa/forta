<div>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Email Blast</h2>
        <div>
            <a href="{{ route('admin.email-blast') }}" class="bg-blue-600 text-white font-bold py-2 px-4 rounded shadow hover:bg-blue-700">Buat Kampanye Baru</a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white rounded shadow-sm p-4">
        <div class="mb-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari subject kampanye..." class="shadow border rounded w-1/3 py-2 px-3 text-gray-700">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Tanggal Buat</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Subject</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Target</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Mode</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Penerima</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="py-3 px-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($blasts as $blast)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $blast->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-sm text-gray-800 font-medium">{{ $blast->subject }}</td>
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $blast->target_description }}</td>
                            <td class="py-3 px-4 text-sm text-gray-800 uppercase">{{ $blast->delivery_mode }}</td>
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $blast->total_recipients }}</td>
                            <td class="py-3 px-4 text-sm">
                                @if($blast->status === 'completed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                                @elseif($blast->status === 'sending' || $blast->status === 'queued')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Proses</span>
                                @elseif($blast->status === 'failed')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ ucfirst($blast->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-center">
                                <a href="{{ route('admin.email-blast.detail', $blast->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-center text-gray-500">Belum ada riwayat email blast.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $blasts->links() }}
        </div>
    </div>
</div>
