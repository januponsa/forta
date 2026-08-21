<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Permohonan Surat Magang</h2>
        <p class="text-sm text-gray-600 mt-1">Kelola dan tinjau semua pengajuan surat pengantar kerja praktik mahasiswa.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="w-full sm:w-1/3">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Cari nama, NIM, atau perusahaan...">
                </div>
            </div>
            
            <div class="w-full sm:w-1/4">
                <select wire:model.live="statusFilter" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    <option value="">Semua Status</option>
                    <option value="submitted">Menunggu Review</option>
                    <option value="under_review">Sedang Direview</option>
                    <option value="revision_required">Perlu Revisi</option>
                    <option value="approved">Disetujui</option>
                    <option value="generated">Surat Tercetak</option>
                    <option value="rejected">Ditolak</option>
                </select>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemohon</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan Magang</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal & Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Surat</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($requests as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $req->student->name ?? 'Unknown' }}</div>
                            <div class="text-sm text-gray-500">{{ $req->student->nim ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 max-w-xs truncate" title="{{ $req->company_name }}">{{ $req->company_name }}</div>
                            <div class="text-sm text-gray-500 max-w-xs truncate" title="{{ $req->company_city }}">{{ $req->company_city }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 mb-1">{{ $req->created_at->format('d M Y') }}</div>
                            @php
                                $statusColors = [
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'under_review' => 'bg-yellow-100 text-yellow-800',
                                    'revision_required' => 'bg-red-100 text-red-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'generated' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                ];
                                $statusLabels = [
                                    'submitted' => 'Menunggu Review',
                                    'under_review' => 'Sedang Direview',
                                    'revision_required' => 'Perlu Revisi',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    'generated' => 'Surat Selesai',
                                    'completed' => 'Surat Selesai',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$req->status] ?? ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $req->letter_number ?? 'Belum ada' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.internship-letters.show', $req->id) }}" class="inline-flex items-center rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Detail
                            </a>
                            <button wire:click="delete({{ $req->id }})" onclick="confirm('Hapus permanen pengajuan surat ini?') || event.stopImmediatePropagation()" class="inline-flex items-center rounded-md bg-red-50 px-2.5 py-1.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-200 hover:bg-red-100 ml-2">
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                            Belum ada pengajuan surat yang sesuai kriteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($requests->hasPages())
            <div class="px-6 py-3 bg-gray-50 border-t">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
