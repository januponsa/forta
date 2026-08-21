<div>
    @section('title', 'Manajemen Pengajuan')

    <div class="mb-4 flex flex-col md:flex-row justify-between md:items-center gap-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Pengajuan Form</h2>
        
        <div class="flex flex-wrap gap-2">
            <button wire:click="export" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm shadow">
                Export Excel
            </button>
            <input type="text" wire:model.live="search" placeholder="Cari Nama/NIM..." class="shadow border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-sm">
            
            <select wire:model.live="formFilter" class="shadow border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-sm">
                <option value="">Semua Formulir</option>
                @foreach($forms as $f)
                    <option value="{{ $f->id }}">{{ $f->title }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="shadow border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-sm">
                <option value="">Semua Status</option>
                <option value="submitted">Submitted</option>
                <option value="revision">Revision</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Formulir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($submissions as $sub)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $sub->name }}<br>
                                <span class="text-gray-500 font-normal text-xs">{{ $sub->nim }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sub->form->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sub->submitted_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $sub->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($sub->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                      ($sub->status == 'revision' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.submissions.show', $sub->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                
                                @if($sub->status !== 'approved')
                                    <button wire:click="updateStatus({{ $sub->id }}, 'approved')" class="text-green-600 hover:text-green-900 mr-3">Approve</button>
                                @endif
                                
                                @if($sub->status !== 'revision')
                                    <button wire:click="updateStatus({{ $sub->id }}, 'revision')" class="text-yellow-600 hover:text-yellow-900 mr-3">Revisi</button>
                                @endif

                                @if($sub->status !== 'rejected')
                                    <button wire:click="updateStatus({{ $sub->id }}, 'rejected')" class="text-red-600 hover:text-red-900">Reject</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada pengajuan ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $submissions->links() }}
    </div>
</div>
