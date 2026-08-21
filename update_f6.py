import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\f6.blade.php'

content = """@extends('pdf.defense.layout')

@section('title', 'F6 - Saran')

@section('content')
<div class="document-title">
    DAFTAR SARAN DAN PERBAIKAN
</div>

@php
    $student = $case->student;
    
    // Process Suggestions
    $alatSugs = [];
    $laporanSugs = [];
    
    if (isset($suggestions) && count($suggestions) > 0) {
        foreach ($suggestions as $sug) {
            $cat = strtolower($sug->category ?? '');
            
            $status = 'Belum Dikerjakan';
            if ($sug->status === 'in_progress') $status = 'Sedang Dikerjakan';
            elseif ($sug->status === 'completed') $status = 'Sudah Diperbaiki';
            elseif ($sug->status === 're-revision') $status = 'Perlu Perbaikan Ulang';
            elseif ($sug->status === 'approved') $status = 'Disetujui';
            
            $item = [
                'text' => $sug->suggestion,
                'giver' => $sug->lecturer->name ?? '-',
                'role' => $sug->lecturer_role === 'examiner' ? 'Penguji' : 'Pembimbing',
                'status' => $status
            ];
            
            if (str_contains($cat, 'alat') || str_contains($cat, 'produk')) {
                $alatSugs[] = $item;
            } else {
                $laporanSugs[] = $item; // Default fallback for everything else
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
            <th style="width: 5%; text-align: center;">No</th>
            <th style="width: 45%; text-align: left;">Saran / Perbaikan</th>
            <th style="width: 25%; text-align: left;">Dosen Pemberi (Peran)</th>
            <th style="width: 25%; text-align: center;">Status</th>
        </tr>
    </thead>
    <tbody>
        <!-- Bagian A: Alat / Produk -->
        <tr>
            <td colspan="4" style="font-weight: bold; background-color: #eaeaea;">A. Penyempurnaan Alat/Produk</td>
        </tr>
        @if(count($alatSugs) > 0)
            @foreach($alatSugs as $idx => $sug)
            <tr>
                <td style="text-align: center; vertical-align: top;">{{ $idx + 1 }}</td>
                <td style="text-align: justify; vertical-align: top;">{{ $sug['text'] }}</td>
                <td style="vertical-align: top;">{{ $sug['giver'] }}<br><span style="font-size: 9pt; color: #555;">({{ $sug['role'] }})</span></td>
                <td style="text-align: center; vertical-align: top;">{{ $sug['status'] }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4" style="text-align: center; font-style: italic; color: #555;">Tidak ada saran/perbaikan pada bagian ini.</td>
            </tr>
        @endif
        
        <!-- Bagian B: Laporan -->
        <tr>
            <td colspan="4" style="font-weight: bold; background-color: #eaeaea;">B. Penyempurnaan Laporan</td>
        </tr>
        @if(count($laporanSugs) > 0)
            @foreach($laporanSugs as $idx => $sug)
            <tr>
                <td style="text-align: center; vertical-align: top;">{{ $idx + 1 }}</td>
                <td style="text-align: justify; vertical-align: top;">{{ $sug['text'] }}</td>
                <td style="vertical-align: top;">{{ $sug['giver'] }}<br><span style="font-size: 9pt; color: #555;">({{ $sug['role'] }})</span></td>
                <td style="text-align: center; vertical-align: top;">{{ $sug['status'] }}</td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4" style="text-align: center; font-style: italic; color: #555;">Tidak ada saran/perbaikan pada bagian ini.</td>
            </tr>
        @endif
    </tbody>
</table>

@php
    $supervisor = $case->assignments->where('role', 'supervisor')->first();
    $examiner = $case->assignments->where('role', 'examiner')->first();
@endphp

<div class="signature-block">
    <div style="text-align: center; margin-bottom: 10px;">Tangerang, {{ $signatures['date'] ?? '-' }}</div>
    <div class="signature-left">
        <div>Dosen Penguji</div>
        <div class="signature-img">
            @if(isset($signatures['examiner']) && $signatures['examiner'])
                <img src="{{ $signatures['examiner'] }}" style="max-height: 60px;">
            @endif
        </div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $examiner->lecturer->name ?? '-' }}</div>
        <div>NIDN {{ $examiner->lecturer->nip ?? '-' }}</div>
    </div>
    
    <div class="signature-right">
        <div>Dosen Pembimbing</div>
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
