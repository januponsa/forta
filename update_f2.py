import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\f2.blade.php'

content = """@extends('pdf.defense.layout')

@section('title', 'F2 - Rekap Nilai')

@section('content')
<div class="document-title" style="margin-bottom: 15px;">
    REKAPITULASI<br>NILAI UJIAN LAPORAN KERJA PRAKTIK
</div>

@php
    $student = $case->student;
    $supervisor = $case->assignments->where('role', 'supervisor')->first();
    $examiner = $case->assignments->where('role', 'examiner')->first();
    
    // Get raw scores if possible
    $np1_raw = $exmAssessment->total_score ?? 0;
    $npem_raw = $spvAssessment->total_score ?? 0;
    
    // For mentor, calculate raw directly from scores to ensure full precision
    $np2_raw = 0;
    if (isset($menAssessment)) {
        $menScores = $menAssessment->scores()->pluck('score');
        if ($menScores->count() > 0) {
            $np2_raw = $menScores->sum() / $menScores->count();
        }
    }
    
    $np1_display = rtrim(rtrim(number_format($np1_raw, 2, '.', ''), '0'), '.');
    $np2_display = rtrim(rtrim(number_format($np2_raw, 2, '.', ''), '0'), '.');
    $npem_display = rtrim(rtrim(number_format($npem_raw, 2, '.', ''), '0'), '.');
    
    $final_raw = ($np1_raw * 0.3) + ($np2_raw * 0.4) + ($npem_raw * 0.3);
    $final_display = number_format($final_raw, 2, '.', '');
    
    // Konversi Huruf Mutu
    $huruf_mutu = 'E';
    if ($final_raw >= 90) $huruf_mutu = 'A';
    elseif ($final_raw >= 85) $huruf_mutu = 'A-';
    elseif ($final_raw >= 80) $huruf_mutu = 'B+';
    elseif ($final_raw >= 75) $huruf_mutu = 'B';
    elseif ($final_raw >= 70) $huruf_mutu = 'B-';
    elseif ($final_raw >= 65) $huruf_mutu = 'C+';
    elseif ($final_raw >= 60) $huruf_mutu = 'C';
    elseif ($final_raw >= 50) $huruf_mutu = 'D';
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
    <tr>
        <td style="border: none; padding: 2px 0; vertical-align: top;">Pembimbing</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">:</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">{{ $supervisor->lecturer->name ?? '-' }}</td>
    </tr>
</table>

<table class="table-data" style="margin-bottom: 20px;">
    <thead>
        <tr>
            <th>Komponen</th>
            <th style="width: 15%;">Kode</th>
            <th style="width: 15%; text-align: right;">Bobot</th>
            <th style="width: 20%; text-align: right;">Nilai Mentah</th>
            <th style="width: 20%; text-align: right;">Kontribusi</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Nilai Penguji</td>
            <td style="text-align: center;">NP1</td>
            <td style="text-align: right;">30%</td>
            <td style="text-align: right;">{{ $np1_display }}</td>
            <td style="text-align: right;">{{ number_format($np1_raw * 0.3, 2, '.', '') }}</td>
        </tr>
        <tr>
            <td>Nilai Mentor</td>
            <td style="text-align: center;">NP2</td>
            <td style="text-align: right;">40%</td>
            <td style="text-align: right;">{{ $np2_display }}</td>
            <td style="text-align: right;">{{ number_format($np2_raw * 0.4, 2, '.', '') }}</td>
        </tr>
        <tr>
            <td>Nilai Pembimbing</td>
            <td style="text-align: center;">NPem</td>
            <td style="text-align: right;">30%</td>
            <td style="text-align: right;">{{ $npem_display }}</td>
            <td style="text-align: right;">{{ number_format($npem_raw * 0.3, 2, '.', '') }}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">NILAI AKHIR (Angka Mutu)</td>
            <td style="text-align: right; font-weight: bold; background-color: #f9f9f9;">{{ $final_display }}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">HURUF MUTU</td>
            <td style="text-align: right; font-weight: bold; background-color: #f9f9f9; font-size: 14pt;">{{ $huruf_mutu }}</td>
        </tr>
    </tbody>
</table>

<div style="font-size: 10pt; line-height: 1.4; margin-bottom: 30px;">
    <strong>Keterangan:</strong><br>
    Nilai Akhir = (NP1 &times; 30%) + (NP2 &times; 40%) + (NPem &times; 30%)<br>
    NP1 = Nilai Penguji<br>
    NP2 = Nilai Mentor<br>
    NPem = Nilai Pembimbing
</div>

<div class="signature-block">
    <div class="signature-left">
    </div>
    <div class="signature-right">
        <div>Tangerang, {{ $signatures['date'] ?? '-' }}</div>
        <div>Ketua Penguji,</div>
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
