<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        @if($registrationRequest->status === 'pending')
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100 mb-4">
                <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900">Menunggu Persetujuan</h2>
            <p class="mt-2 text-sm text-slate-600">
                Pendaftaran akun Anda ({{ $registrationRequest->normalized_email }}) sedang diproses oleh administrator Program Studi Informatika.
            </p>
        @elseif($registrationRequest->status === 'approved')
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900">Akun Disetujui</h2>
            <p class="mt-2 text-sm text-slate-600">
                Pendaftaran akun Anda telah disetujui. Silakan masuk.
            </p>
            <div class="mt-6">
                <a href="{{ route('student.login') }}" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700">
                    Masuk Sekarang
                </a>
            </div>
        @elseif($registrationRequest->status === 'rejected')
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900">Pendaftaran Ditolak</h2>
            <p class="mt-2 text-sm text-slate-600">
                Mohon maaf, pendaftaran akun Anda ditolak oleh administrator.
            </p>
            @if($registrationRequest->review_note)
                <div class="mt-4 p-4 bg-slate-100 rounded-md text-left text-sm text-slate-800 border border-slate-200">
                    <strong>Catatan Admin:</strong> {{ $registrationRequest->review_note }}
                </div>
            @endif
        @endif

        <div class="mt-8 text-center text-sm">
            <a href="{{ route('home') }}" class="font-medium text-teal-600 hover:text-teal-500">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
