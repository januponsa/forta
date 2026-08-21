<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORTA - Form & Registrasi Terpadu Pradita</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center h-screen">
    <div class="bg-white p-10 rounded-lg shadow-lg w-full max-w-md text-center">
        <img src="https://pradita.ac.id/assets/img/logo-pradita.png" alt="Pradita Logo" class="mx-auto h-20 mb-6">
        <h1 class="text-3xl font-bold mb-2 text-gray-800">FORTA</h1>
        <p class="text-gray-600 mb-8">Form & Registrasi Terpadu Akademik<br>Universitas Pradita</p>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm text-left">
                <strong>Error!</strong> {{ session('error') }}
            </div>
        @endif

        <a href="{{ route('google.login') }}" class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-md focus:outline-none focus:shadow-outline transition duration-150">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"/></svg>
            Login dengan SSO Pradita
        </a>
    </div>
</body>
</html>
