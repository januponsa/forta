<div>
    @section('title', 'Detail Pengajuan: ' . $submission->form->title)

    <div class="mb-4">
        <a href="{{ route('admin.submissions') }}" class="text-blue-600 hover:underline mb-2 inline-block">&larr; Kembali ke Daftar Pengajuan</a>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Student Info, Answers, and Files (Span 2) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Student Info --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center border-b border-gray-200 bg-gray-50">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Informasi Mahasiswa & Pengajuan</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Detail data mahasiswa dan status form.</p>
                    </div>
                    <div>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $submission->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                ($submission->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                ($submission->status == 'revision' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')) }}">
                            Status: {{ ucfirst($submission->status) }}
                        </span>
                    </div>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $submission->name }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Email Pradita</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono">{{ $submission->email }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">NIM</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-mono">{{ $submission->nim }}</dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Waktu Submit</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $submission->submitted_at->format('d M Y, H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Answers with Per-Field Review --}}
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Jawaban Form</h3>
                    <p class="mt-1 text-xs text-gray-500">Lakukan verifikasi status per kolom jawaban jika diperlukan.</p>
                </div>
                <div class="border-t border-gray-200">
                    <div class="divide-y divide-gray-200">
                        @foreach($submission->form->fields as $field)
                            @if($field->type !== 'file')
                                <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:bg-gray-50">
                                    <div class="flex-1">
                                        <label class="block text-xs font-semibold text-gray-400 uppercase mb-1">{{ $field->label }}</label>
                                        <div class="text-sm text-gray-900 whitespace-pre-wrap font-medium">
                                            @if(is_array($submission->answers[$field->id] ?? ''))
                                                {{ implode(', ', $submission->answers[$field->id]) }}
                                            @else
                                                {{ $submission->answers[$field->id] ?? '-' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        {{-- Current Status Badge --}}
                                        @if(isset($fieldReviewStatuses[$field->id]))
                                            <span class="text-xs px-2.5 py-0.5 rounded-full font-bold uppercase
                                                {{ $fieldReviewStatuses[$field->id] === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $fieldReviewStatuses[$field->id] === 'approved' ? 'Approved' : 'Rejected' }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Diperiksa</span>
                                        @endif

                                        {{-- Toggle buttons --}}
                                        <div class="inline-flex rounded-md shadow-sm ml-2">
                                            <button wire:click="updateFieldReviewStatus({{ $field->id }}, 'approved')" class="bg-green-50 hover:bg-green-200 text-green-700 font-bold py-1 px-2.5 rounded-l text-xs border border-gray-200">
                                                ✓ Setuju
                                            </button>
                                            <button wire:click="updateFieldReviewStatus({{ $field->id }}, 'rejected')" class="bg-red-50 hover:bg-red-200 text-red-700 font-bold py-1 px-2.5 rounded-r text-xs border-y border-r border-gray-200">
                                                ✗ Tolak
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Files with Per-File Review --}}
            @if($submission->files->count() > 0)
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Berkas Lampiran & Verifikasi Dokumen</h3>
                        <p class="mt-1 text-xs text-gray-500">Tinjau isi file, tentukan status kelayakan, dan berikan catatan review.</p>
                    </div>
                    <div class="border-t border-gray-200 p-6 space-y-6">
                        @foreach($submission->files as $file)
                            <div class="p-4 border rounded bg-gray-50 hover:bg-white transition flex flex-col md:flex-row justify-between items-start gap-4">
                                <div class="flex-1 space-y-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <div class="text-sm font-bold text-gray-800">{{ optional($file->field)->label }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $file->original_name }} ({{ round($file->size_bytes / 1024, 2) }} KB)</div>
                                        </div>
                                    </div>
                                    
                                    {{-- Review Inputs --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Status Verifikasi</label>
                                            <select wire:model="fileStatuses.{{ $file->id }}" class="border-gray-300 rounded-md shadow-sm text-xs w-full py-1">
                                                <option value="Belum Diperiksa">Belum Diperiksa</option>
                                                <option value="Disetujui">Disetujui</option>
                                                <option value="Ditolak">Ditolak</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan Koreksi (Review Note)</label>
                                            <input type="text" wire:model="fileNotes.{{ $file->id }}" class="border-gray-300 rounded-md shadow-sm text-xs w-full py-1 px-2" placeholder="Tulis catatan jika dokumen ditolak/revisi...">
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 items-end justify-between self-stretch">
                                    <a href="{{ route('admin.submissions.file', $file->id) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-bold bg-blue-5 hover:bg-blue-100 border border-blue-200 px-3 py-1.5 rounded flex items-center gap-1">
                                        ⬇ Download File
                                    </a>
                                    <button wire:click="updateFileReview({{ $file->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-3 py-1.5 rounded shadow">
                                        Simpan Review
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        {{-- Right Column: Reviewer Assignment & Decisions --}}
        <div class="space-y-6">
            
            {{-- Reviewer Assignment Card --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-md font-bold text-gray-800 border-b pb-2 mb-4">Penugasan Reviewer</h3>
                
                {{-- Assign Form --}}
                <div class="mb-6 bg-gray-50 p-4 border rounded">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Dosen / Reviewer Baru</label>
                    <div class="flex gap-2">
                        <select wire:model="selectedReviewerId" class="border-gray-300 rounded-md shadow-sm text-xs flex-1">
                            <option value="">-- Pilih Reviewer --</option>
                            @foreach($availableReviewers as $rev)
                                <option value="{{ $rev->id }}">{{ $rev->name }} ({{ ucfirst($rev->role) }})</option>
                            @endforeach
                        </select>
                        <button wire:click="assignReviewer" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-2 rounded shadow">
                            Tugaskan
                        </button>
                    </div>
                </div>

                {{-- Assigned Reviewers List --}}
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Daftar Tim Reviewer</h4>
                    
                    @forelse($submission->reviewerAssignments as $assignment)
                        <div class="p-3 border rounded bg-white hover:shadow-sm transition space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-bold text-gray-800">{{ $assignment->user->name }}</div>
                                    <div class="text-xs text-gray-400 capitalize">{{ $assignment->user->role }}</div>
                                </div>
                                <button wire:click="removeReviewer({{ $assignment->id }})" class="text-red-500 hover:text-red-700 text-xs font-bold" onclick="confirm('Yakin ingin melepas reviewer ini?') || event.stopImmediatePropagation()">
                                    Lepas
                                </button>
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-1 border-t text-xs">
                                <span class="text-gray-500">Status Review:</span>
                                <select 
                                    wire:change="updateAssignmentStatus({{ $assignment->id }}, $event.target.value)" 
                                    class="border-gray-300 rounded text-xs py-0.5 px-1.5 bg-gray-50
                                        {{ $assignment->status === 'Selesai' ? 'text-green-700 font-bold' : ($assignment->status === 'Sedang Diperiksa' ? 'text-yellow-700 font-bold' : 'text-gray-600') }}"
                                >
                                    <option value="Belum Diperiksa" {{ $assignment->status === 'Belum Diperiksa' ? 'selected' : '' }}>Belum Diperiksa</option>
                                    <option value="Sedang Diperiksa" {{ $assignment->status === 'Sedang Diperiksa' ? 'selected' : '' }}>Sedang Diperiksa</option>
                                    <option value="Selesai" {{ $assignment->status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-400 italic text-center py-4">Belum ada reviewer yang ditugaskan.</div>
                    @endforelse
                </div>
            </div>

            {{-- Submission Action Decisions --}}
            <div class="bg-white shadow rounded-lg p-6 space-y-4">
                <h3 class="text-md font-bold text-gray-800 border-b pb-2">Keputusan Akhir</h3>
                <p class="text-xs text-gray-500">Gunakan tindakan ini untuk mengubah status persetujuan akhir pengajuan mahasiswa ini.</p>
                
                <div class="flex flex-col gap-2 pt-2">
                    @if($submission->status !== 'approved')
                        <button wire:click="updateStatus('approved')" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow text-sm w-full text-center">
                            ✓ Approve (Setujui Pengajuan)
                        </button>
                    @endif
                    
                    @if($submission->status !== 'revision')
                        <button wire:click="updateStatus('revision')" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded shadow text-sm w-full text-center">
                            ⚠ Minta Revisi Dokumen
                        </button>
                    @endif
                    
                    @if($submission->status !== 'rejected')
                        <button wire:click="updateStatus('rejected')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow text-sm w-full text-center">
                            ✗ Reject (Tolak Pengajuan)
                        </button>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>
