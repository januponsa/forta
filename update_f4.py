import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\f4.blade.php'

content = """@extends('pdf.defense.layout')

@section('title', 'F4 - Evaluasi Penguji')

@section('content')
<div class="document-title">
    HASIL EVALUASI PENGUJI-1 / KETUA
</div>

@php
    $student = $case->student;
    $examiner = $case->assignments->where('role', 'examiner')->first();
    
    // Process rubrics
    $sections = [];
    $na = 0;
    $nb = 0;
    $nc = 0;
    $total = 0;
    
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
                    } elseif (str_starts_with($item->code, 'B')) {
                        $nb += $score->score;
                    } elseif (str_starts_with($item->code, 'C')) {
                        $nc += $score->score;
                    }
                    $total += $score->score;
                }
            }
        }
    }
    
    $originalityStatus = $assessment->originality_status ?? 'Tidak Ada Indikasi Pelanggaran';
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

<table class="table-data" style="margin-bottom: 10px;">
    <thead>
        <tr>
            <th style="text-align: left;">Penilaian</th>
            <th style="width: 20%; text-align: center;">Bobot (Jangkauan Nilai)</th>
            <th style="width: 15%; text-align: center;">Nilai</th>
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
            <td colspan="2" style="text-align: right; font-weight: bold;">Subtotal NA (Pemahaman Isi Laporan)</td>
            <td style="text-align: center; font-weight: bold;">{{ $na }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold;">Subtotal NB (Laporan Kerja Praktik)</td>
            <td style="text-align: center; font-weight: bold;">{{ $nb }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold;">Subtotal NC (Sidang/Seminar Kerja Praktik)</td>
            <td style="text-align: center; font-weight: bold;">{{ $nc }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; background-color: #f9f9f9;">TOTAL NILAI PENGUJI (NA + NB + NC)</td>
            <td style="text-align: center; font-weight: bold; background-color: #f9f9f9; font-size: 12pt;">{{ $total }}</td>
        </tr>
    </tbody>
</table>

<table class="table-data" style="margin-bottom: 20px;">
    <thead>
        <tr>
            <th style="text-align: left;">D. Originalitas</th>
            <th style="width: 35%; text-align: center;">Status Originalitas</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: justify; font-size: 10pt;">Jika pada saat sidang kerja praktik mahasiswa terbukti melakukan plagiarisme, mahasiswa dinyatakan gagal.</td>
            <td style="text-align: center; font-weight: bold; vertical-align: middle;">
                @if($originalityStatus === 'Terbukti Plagiarisme')
                    <span style="color: red;">{{ $originalityStatus }}</span>
                @else
                    {{ $originalityStatus }}
                @endif
            </td>
        </tr>
    </tbody>
</table>

<div class="signature-block">
    <div class="signature-left">
    </div>
    <div class="signature-right">
        <div>Tangerang, {{ $signatures['date'] ?? '-' }}</div>
        <div>Ketua/Penguji,</div>
        <div class="signature-img">
            @if(isset($signatures['examiner']) && $signatures['examiner'])
                <img src="{{ $signatures['examiner'] }}" style="max-height: 60px;">
            @endif
        </div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $examiner->lecturer->name ?? '-' }}</div>
        <div>NIDN {{ $examiner->lecturer->nip ?? '-' }}</div>
    </div>
    <div class="clear"></div>
</div>
@endsection
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
