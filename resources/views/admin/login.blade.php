@extends('layouts.guest')

@section('title', 'Masuk Sistem FORTA')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="flex justify-center mb-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-teal-100 text-teal-800 border border-teal-200">
                Single Sign-On &bull; Deteksi Role Otomatis
            </span>
        </div>
        <h2 class="text-center text-3xl font-extrabold text-slate-900">
            Masuk ke Sistem FORTA
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
            Portal Terpadu Mahasiswa, Dosen, &amp; Pengelola Prodi Informatika
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-xl sm:px-10 border border-slate-200">
            @if(session('error'))
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Unified Google OAuth Button -->
            <div class="mb-6">
                <a href="{{ route('auth.google.redirect') }}" class="w-full flex items-center justify-center py-3 px-4 border border-slate-300 rounded-lg shadow-sm bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/><path d="M1 1h22v22H1z" fill="none"/></svg>
                    <span>Masuk dengan Google Pradita</span>
                </a>
                <p class="mt-2 text-center text-xs text-slate-500">
                    Mendukung <strong>@student.pradita.ac.id</strong> (Mahasiswa) &amp; <strong>@pradita.ac.id</strong> (Dosen / Admin)
                </p>
            </div>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase tracking-wider">
                        <span class="px-3 bg-white text-slate-400 font-semibold">Atau masuk dengan akun kredensial</span>
                    </div>
                </div>

                <form class="space-y-5 mt-6" action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700">
                            Username / Email / NIP
                        </label>
                        <div class="mt-1">
                            <input id="username" name="username" type="text" autocomplete="username" required value="{{ old('username') }}" placeholder="Masukkan username, email, atau NIP Dosen" class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                        </div>
                        @error('username') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan password akun Anda" class="appearance-none block w-full px-3 py-2.5 border border-slate-300 rounded-lg shadow-sm placeholder-slate-400 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-slate-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-slate-700">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition">
                            Masuk
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6 pt-5 border-t border-slate-100 bg-slate-50/70 -mx-4 px-4 sm:-mx-10 sm:px-10 pb-2 rounded-b-xl">
                <div class="text-xs text-slate-500 space-y-1.5">
                    <p><span class="font-semibold text-slate-700">Mahasiswa:</span> Klik tombol Google menggunakan email <span class="font-mono text-teal-700">@student.pradita.ac.id</span>.</p>
                    <p><span class="font-semibold text-slate-700">Dosen &amp; Pengelola:</span> Gunakan tombol Google (<span class="font-mono text-teal-700">@pradita.ac.id</span>) atau form Username / NIP &amp; Password di atas.</p>
                </div>
            </div>
            
            <div class="mt-6 text-center text-sm">
                <a href="{{ route('home') }}" class="font-medium text-teal-600 hover:text-teal-500">
                    &larr; Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
