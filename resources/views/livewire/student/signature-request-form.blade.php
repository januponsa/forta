<div>
    <div class="mb-4">
        <h2 class="text-2xl font-semibold text-gray-800">Form Pengajuan Tanda Tangan</h2>
    </div>

    <form wire:submit.prevent="store" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Judul Dokumen</label>
                <input type="text" wire:model="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            

        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Jenis Surat (Opsional)</label>
                <input type="text" wire:model="letter_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Keperluan (Opsional)</label>
                <input type="text" wire:model="purpose" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
            <textarea wire:model="notes" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Upload Dokumen PDF</label>
            <input type="file" wire:model.live="document" accept="application/pdf" id="pdfUpload" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @error('document') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>


        <div class="flex items-center justify-end">
            <a href="{{ route('student.signature-requests.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 mr-4">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                <span wire:loading.remove wire:target="store">Kirim Pengajuan</span>
                <span wire:loading wire:target="store">Memproses...</span>
            </button>
        </div>
    </form>
    
    <!-- Scripts for PDF.js and Drag/Drop -->

</div>
