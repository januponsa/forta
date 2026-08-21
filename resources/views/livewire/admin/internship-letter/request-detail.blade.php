<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Detail Permohonan Surat Magang</h2>
            <p class="text-sm text-gray-600 mt-1">Review data pengajuan dan generate PDF resmi.</p>
        </div>
        <a href="{{ route('admin.internship-letters.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Kembali ke Daftar
        </a>
    </div>

    @if (session()->has('message'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border-l-4 border-green-500">
            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Detail Pengajuan -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Pemohon</h3>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-800">
                        Status: {{ strtoupper($request->status) }}
                    </span>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->student->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">NIM</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->student->nim }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Periode Magang</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $request->start_date->format('d M Y') }} s/d 
                                {{ $request->end_date ? $request->end_date->format('d M Y') : '(-)' }}
                                @if($request->duration_notes) ({{ $request->duration_notes }}) @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Posisi/Divisi</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->internship_position ?? '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Tujuan Magang</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $request->purpose ?? 'Standar (Memohon izin kerja praktik)' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Form Koreksi Perusahaan (Hanya jika belum generated) -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Koreksi Data Perusahaan Tujuan</h3>
                    <p class="mt-1 text-xs text-gray-500">Admin dapat memperbaiki typo alamat tanpa harus menolak permohonan.</p>
                </div>
                <div class="p-6">
                    <form wire:submit.prevent="updateCompanyDetails" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                            <input type="text" wire:model="company_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Penerima/Jabatan</label>
                            <input type="text" wire:model="recipient_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat Perusahaan</label>
                            <textarea wire:model="company_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" @if(in_array($request->status, ['generated', 'completed'])) disabled @endif></textarea>
                        </div>
                        @if(!in_array($request->status, ['generated', 'completed']))
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Simpan Perubahan
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Aksi & Riwayat -->
        <div class="space-y-6">
            
            <!-- Aksi Admin -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Aksi</h3>
                </div>
                <div class="p-6 space-y-6">
                    
                    @if(in_array($request->status, ['submitted', 'under_review', 'revision_required', 'approved']))
                    <!-- Setujui & Generate -->
                    <div>
                        <button wire:click="approveAndGenerate" wire:loading.attr="disabled" class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                            <span wire:loading.remove wire:target="approveAndGenerate">Setujui & Generate Surat PDF</span>
                            <span wire:loading wire:target="approveAndGenerate">Memproses PDF...</span>
                        </button>
                        <p class="text-xs text-gray-500 mt-2">Menyetujui akan langsung membuatkan nomor surat yang terkunci dan mencetak PDF.</p>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Minta Revisi -->
                    <form wire:submit.prevent="askRevision">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Revisi</label>
                        <textarea wire:model="revision_note" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm mb-2" placeholder="Jelaskan apa yang perlu diperbaiki..."></textarea>
                        @error('revision_note') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500">
                            Kembalikan untuk Revisi
                        </button>
                    </form>

                    <!-- Tolak -->
                    <form wire:submit.prevent="reject">
                        <label class="block text-sm font-medium text-gray-700 mb-1 mt-4">Alasan Penolakan</label>
                        <textarea wire:model="rejection_reason" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm mb-2" placeholder="Jelaskan mengapa ditolak..."></textarea>
                        @error('rejection_reason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                            Tolak Permanen
                        </button>
                    </form>
                    @endif

                    @if(in_array($request->status, ['generated', 'completed']))
                    <div>
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md">
                            <p class="text-sm font-medium text-green-800">Surat telah di-generate.</p>
                            <p class="text-xs text-green-700 mt-1">No: {{ $request->letter_number }}</p>
                        </div>
                        <button wire:click="downloadPdf" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Download PDF Final
                        </button>

                        <button wire:click="resendEmail" wire:loading.attr="disabled" class="w-full mt-3 inline-flex justify-center items-center rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-500">
                            <span wire:loading.remove wire:target="resendEmail">Kirim Notifikasi Email</span>
                            <span wire:loading wire:target="resendEmail">Mengirim Email...</span>
                        </button>
                        
                        <div class="mt-4 border-t pt-4">
                            <button wire:click="approveAndGenerate" onclick="confirm('Yakin ingin men-generate ulang PDF? Nomor surat akan tetap sama, hanya merender ulang PDF.') || event.stopImmediatePropagation()" class="w-full inline-flex justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Generate Ulang PDF
                            </button>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            <!-- Audit Trail -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Riwayat Audit</h3>
                </div>
                <div class="p-6">
                    <ul role="list" class="space-y-4">
                        @foreach($request->histories()->orderBy('created_at', 'desc')->get() as $history)
                        <li>
                            <div class="flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center ring-8 ring-white">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-medium text-gray-900">{{ $history->actor->name ?? 'Sistem' }}</span> 
                                            {{ str_replace('_', ' ', $history->action) }}
                                        </p>
                                        @if($history->note)
                                        <p class="text-xs text-gray-600 mt-1 italic">"{{ $history->note }}"</p>
                                        @endif
                                    </div>
                                    <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                        {{ $history->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
