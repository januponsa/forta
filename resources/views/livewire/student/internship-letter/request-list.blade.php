<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Layanan Surat Pengantar Magang</h2>
            <p class="text-sm text-gray-600 mt-1">Kelola permohonan surat kerja praktik Anda.</p>
        </div>
        <a href="{{ route('student.internship-letters.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
            </svg>
            Buat Permohonan Baru
        </a>
    </div>

    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border-l-4 border-green-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan & Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Surat</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($requests as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $req->company_name }}</div>
                            <div class="text-sm text-gray-500">{{ $req->start_date->format('d M Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'under_review' => 'bg-yellow-100 text-yellow-800',
                                    'revision_required' => 'bg-red-100 text-red-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'generated' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                ];
                                $statusLabels = [
                                    'draft' => 'Draft',
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
                            
                            @if($req->status === 'revision_required' && $req->revision_note)
                                <div class="mt-1 text-xs text-red-600 max-w-xs truncate" title="{{ $req->revision_note }}">
                                    Catatan: {{ $req->revision_note }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $req->letter_number ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if(in_array($req->status, ['draft', 'revision_required']))
                                <a href="{{ route('student.internship-letters.edit', $req->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit / Perbaiki</a>
                            @endif

                            @if(in_array($req->status, ['generated', 'completed']))
                                <button wire:click="downloadPdf({{ $req->id }})" class="text-green-600 hover:text-green-900 inline-flex items-center">
                                    <svg class="mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Unduh PDF
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Belum ada pengajuan surat pengantar magang.
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
