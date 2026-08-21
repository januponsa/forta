@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-md border border-gray-100 p-6 shadow-sm">
            <div class="text-sm font-medium text-gray-500 truncate">Total Forms</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ \App\Models\Form::count() }}</div>
        </div>
        <div class="bg-white rounded-md border border-gray-100 p-6 shadow-sm">
            <div class="text-sm font-medium text-gray-500 truncate">Active Forms</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ \App\Models\Form::where('status', 'active')->count() }}</div>
        </div>
        <div class="bg-white rounded-md border border-gray-100 p-6 shadow-sm">
            <div class="text-sm font-medium text-gray-500 truncate">Total Submissions</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ \App\Models\Submission::count() }}</div>
        </div>
    </div>
@endsection
