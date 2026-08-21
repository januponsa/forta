import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\f3.blade.php'

content = """@extends('pdf.defense.layout')

@section('title', 'F3 - Evaluasi Pembimbing')

@section('content')
<div class="document-title">
    HASIL EVALUASI PEMBIMBING
</div>

@php
    $student = $case->student;
    $supervisor = $case->assignments->where('role', 'supervisor')->first();
    
    // Process rubrics
    $sections = [];
    $na = 0;
    $nb = 0;
    $maxNa = 0;
    $maxNb = 0;
    $total = 0;
    $maxTotal = 0;
    
    if (isset($assessment)) {
        foreach ($assessment->scores as $score) {
            $item = $score->rubricItem;
            if ($item) {
                $section = $item->section;
                if ($section) {
                    $sections[$section->id]['name'] = $section->name;
                    $sections[$section->id]['items'][] = [
                        'code' => $item->code,
                        'description' => $item->description,
                        'max' => $item->max_score,
                        'score' => $score->score
                    ];
                    
                    if (str_starts_with($item->code, 'A')) {
                        $na += $score->score;
                        $maxNa += $item->max_score;
                    } elseif (str_starts_with($item->code, 'B')) {
                        $nb += $score->score;
                        $maxNb += $item->max_score;
                    }
                    $total += $score->score;
                    $maxTotal += $item->max_score;
                }
            }
        }
    }
@endphp

<table style="border: none; width: 100%; margin-bottom: 20px;">
    <tr>
        <td style="width: 30%; border: none; padding: 2px 0;">Nama Mahasiswa</td>
        <td style="width: 2%; border: none; padding: 2px 0;">:</td>
        <td style="width: 68%; border: none; padding: 2px 0;">{{ $student->name }}</td>
    </tr>
    <tr>
        <td style="border: none; padding: 2px 0;">NIM</td>
        <td style="border: none; padding: 2px 0;">:</td>
        <td style="border: none; padding: 2px 0;">{{ $student->nim }}</td>
    </tr>
    <tr>
        <td style="border: none; padding: 2px 0;">Program Studi</td>
        <td style="border: none; padding: 2px 0;">:</td>
        <td style="border: none; padding: 2px 0;">Informatika</td>
    </tr>
    <tr>
        <td style="border: none; padding: 2px 0;">Semester</td>
        <td style="border: none; padding: 2px 0;">:</td>
        <td style="border: none; padding: 2px 0;">{{ $student->semester ?? '-' }}</td>
    </tr>
    <tr>
        <td style="border: none; padding: 2px 0; vertical-align: top;">Judul Laporan Kerja Praktik</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">:</td>
        <td style="border: none; padding: 2px 0; text-align: justify; vertical-align: top;">{{ $case->submission->title ?? '-' }}</td>
    </tr>
</table>

<table class="table-data" style="margin-bottom: 20px;">
    <thead>
        <tr>
            <th style="text-align: left;">Hal yang Dinilai</th>
            <th style="width: 20%; text-align: center;">Bobot (Jangkauan Nilai)</th>
            <th style="width: 15%; text-align: center;">Perolehan Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sections as $secId => $sectionData)
            <tr>
                <td colspan="3" style="font-weight: bold; background-color: #eaeaea;">{{ $sectionData['name'] }}</td>
            </tr>
            @foreach($sectionData['items'] as $item)
                <tr>
                    <td style="text-align: justify;"><strong>{{ $item['code'] }} &mdash;</strong> {{ $item['description'] }}</td>
                    <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
                    <td style="text-align: center;">{{ $item['score'] }}</td>
                </tr>
            @endforeach
        @endforeach
        
        <tr>
            <td colspan="3" style="padding: 0; border: none; height: 10px;"></td>
        </tr>
        
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold;">Subtotal NA (Ketekunan Mahasiswa)</td>
            <td style="text-align: center; font-weight: bold;">{{ $na }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold;">Subtotal NB (Isi Laporan)</td>
            <td style="text-align: center; font-weight: bold;">{{ $nb }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; background-color: #f9f9f9;">TOTAL NILAI PEMBIMBING (NA + NB)</td>
            <td style="text-align: center; font-weight: bold; background-color: #f9f9f9; font-size: 12pt;">{{ $total }}</td>
        </tr>
    </tbody>
</table>

<div class="signature-block">
    <div class="signature-left">
    </div>
    <div class="signature-right">
        <div>Tangerang, {{ $signatures['date'] ?? '-' }}</div>
        <div>Pembimbing,</div>
        <div class="signature-img">
            @if(isset($signatures['supervisor']) && $signatures['supervisor'])
                <img src="{{ $signatures['supervisor'] }}" style="max-height: 60px;">
            @endif
        </div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $supervisor->lecturer->name ?? '-' }}</div>
        <div>NIDN {{ $supervisor->lecturer->nip ?? '-' }}</div>
    </div>
    <div class="clear"></div>
</div>
@endsection
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
