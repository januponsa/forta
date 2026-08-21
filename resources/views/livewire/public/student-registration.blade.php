<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">
            Pendaftaran Mahasiswa
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Daftarkan diri Anda untuk mengakses FORTA
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-slate-200">
            
            <div class="mb-6 flex items-center justify-center p-4 bg-teal-50 border border-teal-100 rounded-md">
                @if($google_avatar)
                    <img src="{{ $google_avatar }}" alt="Profile" class="w-10 h-10 rounded-full mr-3 border border-teal-200">
                @else
                    <div class="w-10 h-10 rounded-full bg-teal-200 text-teal-700 flex items-center justify-center font-bold mr-3">
                        {{ substr($name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="text-sm font-medium text-slate-900">{{ $name }}</div>
                    <div class="text-xs text-slate-500">{{ $email }}</div>
                </div>
            </div>

            <form wire:submit.prevent="submitRequest" class="space-y-6">
                <div>
                    <label for="nim" class="block text-sm font-medium text-slate-700">Nomor Induk Mahasiswa (NIM)</label>
                    <div class="mt-1">
                        <input id="nim" wire:model="nim" type="text" required class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                    </div>
                    @error('nim') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap Sesuai SIAKAD</label>
                    <div class="mt-1">
                        <input id="name" wire:model="name" type="text" required class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                    </div>
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="angkatan" class="block text-sm font-medium text-slate-700">Tahun Angkatan</label>
                    <div class="mt-1">
                        <input id="angkatan" wire:model="angkatan" type="number" required class="appearance-none block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                    </div>
                    @error('angkatan') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="confirmData" wire:model="confirmData" type="checkbox" required class="focus:ring-teal-500 h-4 w-4 text-teal-600 border-slate-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="confirmData" class="font-medium text-slate-700">Konfirmasi Data</label>
                        <p class="text-slate-500">Saya menyatakan bahwa data di atas adalah benar milik saya dan sesuai dengan data akademik institusi.</p>
                    </div>
                </div>
                @error('confirmData') <span class="text-xs text-red-500 block">{{ $message }}</span> @enderror

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        Ajukan Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
