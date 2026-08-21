<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Permohonan Surat Magang/Kerja Praktik</h2>
            <p class="text-sm text-gray-600 mt-1">Isi formulir di bawah ini dengan data perusahaan tujuan yang valid.</p>
        </div>
        <a href="{{ route('student.internship-letters.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-8 border border-gray-100">
        <div class="bg-indigo-50/50 border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-800">Data Pemohon</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/30">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <div class="p-2.5 bg-gray-100 border border-gray-200 rounded-md text-gray-600 sm:text-sm">
                    {{ Auth::guard('student')->user()->name }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIM</label>
                <div class="p-2.5 bg-gray-100 border border-gray-200 rounded-md text-gray-600 sm:text-sm">
                    {{ Auth::guard('student')->user()->nim }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="p-2.5 bg-gray-100 border border-gray-200 rounded-md text-gray-600 sm:text-sm">
                    {{ Auth::guard('student')->user()->email }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester Saat Ini</label>
                <div class="p-2.5 bg-gray-100 border border-gray-200 rounded-md text-gray-600 sm:text-sm">
                    {{ Auth::guard('student')->user()->semester ?? 'Tidak Diketahui' }}
                </div>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="submit" class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 space-y-8">
        <!-- Error Alerts -->
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Terdapat {{ $errors->count() }} kesalahan pada isian form:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div>
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Informasi Instansi/Perusahaan Tujuan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="company_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: PT. Pradita Edukasi Teknologi">
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penerima Tujuan / Jabatan <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="recipient_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Standar: Bapak/Ibu HRD</p>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Perusahaan <span class="text-red-500">*</span></label>
                    <textarea wire:model="company_address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Jalan, Nomor, Gedung, Kecamatan, dsb."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kota <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="company_city" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Tangerang">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Penempatan <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="text" wire:model="placement_location" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Jika berbeda dengan alamat perusahaan">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Detail Pelaksanaan Magang</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Posisi/Divisi <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="text" wire:model="internship_position" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Software Engineer Intern">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="start_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-gray-400 font-normal">(Opsional jika mengisi durasi)</span></label>
                    <input type="date" wire:model="end_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi Alternatif <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="text" wire:model="duration_notes" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Isi jika tanggal selesai belum pasti, contoh: 3 Bulan">
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Penggunaan Surat <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <textarea wire:model="purpose" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Kosongkan untuk menggunakan paragraf standar 'Memohon izin kerja praktik'"></textarea>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-4">Lampiran Tambahan</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload File <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="file" wire:model="attachment" accept=".pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <div wire:loading wire:target="attachment" class="mt-2 text-sm text-indigo-600">Mengunggah...</div>
                    <p class="mt-1 text-xs text-gray-500">Maksimal ukuran 2MB. Hanya PDF yang diizinkan. Gunakan jika perusahaan memerlukan kelengkapan CV/Portofolio yang harus digabungkan.</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan untuk Admin</label>
                    <textarea wire:model="additional_notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t">
            <div class="flex items-start">
                <div class="flex h-5 items-center">
                    <input id="declaration" wire:model="declaration" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="declaration" class="font-medium text-gray-700">Pernyataan Kebenaran Data <span class="text-red-500">*</span></label>
                    <p class="text-gray-500">Saya menyatakan bahwa data yang saya masukkan adalah benar. Saya memahami bahwa data yang telah disubmit dan diproses tidak dapat diubah kembali kecuali dikembalikan oleh admin.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-4 border-t">
            <button type="button" wire:click="saveDraft" class="inline-flex items-center rounded-md bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Simpan sebagai Draft
            </button>
            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50">
                <span wire:loading.remove wire:target="submit">Kirim Permohonan</span>
                <span wire:loading wire:target="submit">Memproses...</span>
            </button>
        </div>
    </form>
</div>
