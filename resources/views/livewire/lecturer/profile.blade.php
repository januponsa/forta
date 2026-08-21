<div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Profil & Tanda Tangan</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Perbarui pengaturan profil dan tanda tangan digital Anda di sini. Tanda tangan ini akan digunakan untuk pengesahan dokumen sidang secara otomatis.
                </p>
            </div>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="shadow sm:rounded-md sm:overflow-hidden">
                <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                    
                    @if (session()->has('message'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanda Tangan Saat Ini</label>
                        <div class="mt-2 flex items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md bg-gray-50 h-48 relative">
                            @if($existingSignaturePath)
                                <img src="{{ Storage::url($existingSignaturePath) }}" alt="Tanda Tangan" class="max-h-32 object-contain">
                            @else
                                <div class="text-center text-gray-400">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M24 8l16 16-16 16M8 24h32" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="mt-1 text-sm">Belum ada tanda tangan yang diunggah.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unggah Tanda Tangan Baru</label>
                        <div class="mt-1 flex items-center">
                            <input type="file" wire:model="signature" id="signature-upload" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            "/>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Gunakan gambar dengan background transparan (PNG) untuk hasil terbaik. Maksimal 2MB.</p>
                        @error('signature') <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        
                        <div wire:loading wire:target="signature" class="mt-2 text-sm text-indigo-600">
                            Sedang mengunggah...
                        </div>
                    </div>

                </div>
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <button type="button" wire:click="saveSignature" wire:loading.attr="disabled" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                        <span wire:loading.remove wire:target="saveSignature">Simpan Tanda Tangan</span>
                        <span wire:loading wire:target="saveSignature">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
