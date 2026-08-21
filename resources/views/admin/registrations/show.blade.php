@extends('layouts.admin')

@section('title', 'Review Pendaftaran Mahasiswa')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('admin.registrations') }}" class="text-sm font-medium text-indigo-500 hover:text-indigo-600">&larr; Kembali ke Daftar Pendaftaran</a>
        <h1 class="text-2xl md:text-3xl text-slate-800 font-bold mt-2">Review Pendaftaran Mahasiswa ✨</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-slate-200 p-6 max-w-3xl">
        <div class="sm:flex sm:items-start mb-6">
            <div class="w-16 h-16 shrink-0 mr-4">
                @if($registrationRequest->google_avatar)
                    <img class="rounded-full" src="{{ $registrationRequest->google_avatar }}" alt="{{ $registrationRequest->name }}" />
                @else
                    <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 font-bold text-2xl">
                        {{ substr($registrationRequest->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div>
                <h2 class="text-xl font-semibold text-slate-800">{{ $registrationRequest->name }}</h2>
                <p class="text-slate-500">{{ $registrationRequest->google_email }}</p>
                <div class="mt-2 text-sm">
                    <span class="font-medium text-slate-800">Tanggal Pengajuan:</span> 
                    {{ $registrationRequest->requested_at ? $registrationRequest->requested_at->format('d M Y H:i') : '-' }}
                </div>
            </div>
        </div>

        <div class="bg-slate-50 p-4 rounded-md border border-slate-200 mb-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-slate-500">NIM</dt>
                    <dd class="mt-1 text-sm text-slate-900 font-semibold">{{ $registrationRequest->nim }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-slate-500">Angkatan</dt>
                    <dd class="mt-1 text-sm text-slate-900 font-semibold">{{ $registrationRequest->angkatan }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-slate-500">Google ID</dt>
                    <dd class="mt-1 text-sm text-slate-900 font-mono">{{ $registrationRequest->google_id }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-slate-500">Status</dt>
                    <dd class="mt-1 text-sm text-slate-900">
                        @if($registrationRequest->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Menunggu</span>
                        @elseif($registrationRequest->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @if($registrationRequest->conflict_type)
            <div class="mb-6 bg-red-50 p-4 rounded-md text-sm text-red-700 border border-red-200 flex items-start">
                <svg class="w-5 h-5 shrink-0 mr-2 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <strong class="block mb-1">Peringatan Konflik Data:</strong> 
                    @if($registrationRequest->conflict_type === 're_registration')
                        Mahasiswa dengan NIM dan email ini sudah ada namun statusnya inaktif. Penyetujuan akan mengaktifkan kembali akun ini.
                    @elseif($registrationRequest->conflict_type === 'email_mismatch')
                        NIM sudah terdaftar dengan email yang BERBEDA. Penyetujuan akan mengganti akun email mahasiswa tersebut ke email ini!
                    @else
                        {{ $registrationRequest->conflict_type }}
                    @endif
                </div>
            </div>
        @endif

        @if($registrationRequest->status === 'pending')
            <div class="border-t border-slate-200 pt-6">
                <h3 class="text-lg font-medium text-slate-800 mb-4">Tindakan</h3>
                
                <form method="POST" action="{{ route('admin.registrations.approve', $registrationRequest->id) }}" id="approve-form" class="hidden">@csrf</form>
                <form method="POST" action="{{ route('admin.registrations.reject', $registrationRequest->id) }}" id="reject-form" class="hidden">@csrf</form>

                <div class="mb-4">
                    <label for="review_note" class="block text-sm font-medium text-slate-700 mb-1">Catatan Review (Opsional)</label>
                    <textarea id="review_note" name="review_note" form="approve-form" rows="3" class="w-full shadow-sm sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 border-slate-300 rounded-md placeholder-slate-400" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    <script>
                        document.getElementById('review_note').addEventListener('input', function(e) {
                            // Copy value to reject form as well
                            let rejectInput = document.getElementById('reject_note_hidden');
                            if (!rejectInput) {
                                rejectInput = document.createElement('input');
                                rejectInput.type = 'hidden';
                                rejectInput.name = 'review_note';
                                rejectInput.id = 'reject_note_hidden';
                                document.getElementById('reject-form').appendChild(rejectInput);
                            }
                            rejectInput.value = e.target.value;
                        });
                    </script>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit" form="approve-form" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Setujui & Aktifkan
                    </button>
                    <button type="submit" form="reject-form" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Tolak
                    </button>
                </div>
            </div>
        @else
            <div class="border-t border-slate-200 pt-6 mt-6">
                <h3 class="text-lg font-medium text-slate-800 mb-4">Hasil Review</h3>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">Direview Oleh</dt>
                        <dd class="mt-1 text-sm text-slate-900 font-medium">{{ $registrationRequest->reviewer->name ?? 'Sistem' }}</dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-slate-500">Waktu Review</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $registrationRequest->reviewed_at ? $registrationRequest->reviewed_at->format('d M Y H:i') : '-' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-slate-500">Catatan</dt>
                        <dd class="mt-1 text-sm text-slate-900 italic bg-slate-50 p-3 rounded border border-slate-200">
                            {{ $registrationRequest->review_note ?: 'Tidak ada catatan.' }}
                        </dd>
                    </div>
                </dl>
            </div>
        @endif
    </div>
</div>
@endsection
