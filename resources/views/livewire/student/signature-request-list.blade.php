<div>
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-2xl font-semibold text-gray-800">Daftar Pengajuan Tanda Tangan</h2>
        <a href="{{ route('student.signature-requests.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Ajukan Tanda Tangan
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Tanggal</th>
                    <th class="py-2 px-4 border-b">Judul Dokumen</th>
                    <th class="py-2 px-4 border-b">Penandatangan</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr class="hover:bg-gray-100 border-b">
                    <td class="py-2 px-4">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2 px-4">{{ $request->title }}</td>
                    <td class="py-2 px-4">{{ $request->signatory->name ?? '-' }}</td>
                    <td class="py-2 px-4">
                        @if($request->status == 'completed')
                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded-full text-xs">Selesai</span>
                        @elseif($request->status == 'rejected')
                            <span class="px-2 py-1 bg-red-200 text-red-800 rounded-full text-xs">Ditolak</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center">
                        <a href="{{ route('student.signature-requests.show', $request->id) }}" class="text-blue-500 hover:text-blue-700">Detail</a>
                        @if($request->status == 'completed' && $request->signed_file_path)
                            | <a href="{{ Storage::disk('private')->url($request->signed_file_path) }}" target="_blank" class="text-green-600 hover:text-green-800">Unduh Final</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-4 text-center text-gray-500">Anda belum pernah mengajukan dokumen untuk ditandatangani.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">
            {{ $requests->links() }}
        </div>
    </div>
</div>
