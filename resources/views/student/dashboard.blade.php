@extends('layouts.student')

@section('title', 'Dashboard Mahasiswa')

@section('content')
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Selamat datang di FORTA</h2>
        <p class="text-gray-600 mb-6">
            Pilih form akademik yang ingin Anda isi.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(\App\Models\Form::where('status', 'active')->get() as $form)
            <div class="border rounded-lg p-5 hover:shadow-md transition bg-blue-50">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">{{ $form->title }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ Str::limit($form->description, 100) }}</p>
                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mb-4">
                    {{ $form->activityType->name }}
                </span>
                <a href="#" class="block text-center w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    Isi Form
                </a>
            </div>
            @endforeach
        </div>
    </div>
@endsection
