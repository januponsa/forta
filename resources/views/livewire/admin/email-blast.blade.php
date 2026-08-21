<div>
    @section('title', 'Blast Email Mahasiswa')

    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-800">Kirim Email Massal</h2>
        <p class="text-gray-500 text-sm">Kirim pemberitahuan atau informasi ke seluruh mahasiswa yang terdaftar di sistem.</p>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6 max-w-3xl">
        <form wire:submit.prevent="sendEmail">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Subjek Email</label>
                <input type="text" wire:model="subject" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Contoh: Pengingat Batas Pengumpulan TA">
                @error('subject') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Isi Pesan</label>
                <textarea wire:model="message" rows="8" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ketik isi pesan di sini..."></textarea>
                <p class="text-xs text-gray-500 mt-1">Gunakan enter/baris baru seperti biasa. Format teks panjang didukung.</p>
                @error('message') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center shadow"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="sendEmail">
                        Kirim Email ke Semua Mahasiswa
                    </span>
                    <span wire:loading wire:target="sendEmail">
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
