@extends('pdf.defense.layout')

@section('title', 'F6 - Saran')

@section('content')
<div class="document-title">
    DAFTAR SARAN DAN PERBAIKAN
</div>

@php
    $student = $case->student;
    $supervisor = $case->assignments->where('role', 'supervisor')->first();
    $examiner = $case->assignments->where('role', 'examiner')->first();
    $schedule = $case->latestSchedule;
    
    $tanggal = '';
    $bulan = '';
    $tahun = '';
    if ($schedule && $schedule->scheduled_at) {
        $dt = \Carbon\Carbon::parse($schedule->scheduled_at)->locale('id');
        $tanggal = $dt->isoFormat('D');
        $bulan = $dt->isoFormat('MMMM');
        $tahun = $dt->isoFormat('YYYY');
    }
    
    // Process Suggestions
    $alatSugs = [];
    $laporanSugs = [];
    
    if (isset($suggestions) && count($suggestions) > 0) {
        foreach ($suggestions as $sug) {
            $cat = strtolower($sug->category ?? '');
            
            $item = [
                'text' => $sug->suggestion,
                'giver' => $sug->lecturer->name ?? '',
                'role' => $sug->lecturer_role === 'examiner' ? 'Penguji' : 'Pembimbing',
            ];
            
            if (str_contains($cat, 'alat') || str_contains($cat, 'produk')) {
                $alatSugs[] = $item;
            } else {
                $laporanSugs[] = $item;
            }
        }
    }
@endphp

<table class="info-table" style="margin-bottom: 15px;">
    <tr>
        <td class="info-label">Nama Mahasiswa</td>
        <td class="info-colon">:</td>
        <td class="info-value">{{ $student->name }}</td>
    </tr>
    <tr>
        <td>NIM</td>
        <td>:</td>
        <td>{{ $student->nim }}</td>
    </tr>
    <tr>
        <td>Program Studi</td>
        <td>:</td>
        <td>Informatika</td>
    </tr>
    <tr>
        <td>Semester</td>
        <td>:</td>
        <td>{{ $student->semester ?? '' }}</td>
    </tr>
    <tr>
        <td style="vertical-align: top;">Judul Tugas Akhir</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top; text-transform: uppercase;">{{ $case->submission->title ?? '' }}</td>
    </tr>
</table>

<table class="table-data" style="margin-bottom: 15px; font-size: 10pt;">
    <thead>
        <tr>
            <th style="width: 5%; text-align: center;">No</th>
            <th style="text-align: center;">Saran / Perbaikan</th>
            <th style="width: 25%; text-align: center;">Dosen Pemberi</th>
        </tr>
    </thead>
    <tbody>
        <!-- Bagian A: Alat / Produk -->
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">A. Penyempurnaan Alat/Produk</td>
        </tr>
        @if(count($alatSugs) > 0)
            @foreach($alatSugs as $idx => $sug)
            <tr>
                <td style="text-align: center; vertical-align: top;">{{ $idx + 1 }}</td>
                <td style="text-align: justify; vertical-align: top;">{{ $sug['text'] }}</td>
                <td style="vertical-align: top;">{{ $sug['giver'] }}<br><span style="font-size: 9pt; color: #555;">({{ $sug['role'] }})</span></td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3" style="text-align: center; font-style: italic; color: #555;">Tidak ada saran/perbaikan pada bagian ini.</td>
            </tr>
        @endif
        
        <!-- Bagian B: Laporan -->
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">B. Penyempurnaan Laporan</td>
        </tr>
        @if(count($laporanSugs) > 0)
            @foreach($laporanSugs as $idx => $sug)
            <tr>
                <td style="text-align: center; vertical-align: top;">{{ $idx + 1 }}</td>
                <td style="text-align: justify; vertical-align: top;">{{ $sug['text'] }}</td>
                <td style="vertical-align: top;">{{ $sug['giver'] }}<br><span style="font-size: 9pt; color: #555;">({{ $sug['role'] }})</span></td>
            </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3" style="text-align: center; font-style: italic; color: #555;">Tidak ada saran/perbaikan pada bagian ini.</td>
            </tr>
        @endif
    </tbody>
</table>

<table style="border: none; width: 100%; margin-top: 20px;">
    <tr>
        <td style="border: none; text-align: center;" colspan="2">
            Tangerang, &nbsp; {{ $tanggal }} &nbsp; {{ $bulan }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $tahun }}
        </td>
    </tr>
    <tr>
        <td style="border: none; width: 50%; text-align: center; vertical-align: top;">
            <div>Dosen Penguji</div>
            <div style="height: 60px;">
                @if(isset($signatures['examiner']) && $signatures['examiner'])
                    <img src="{{ $signatures['examiner'] }}" style="max-height: 55px;">
                @endif
            </div>
            <div>{{ $examiner->lecturer->name ?? '' }}</div>
            <div>NIDN &nbsp; {{ $examiner->lecturer->nip ?? '' }}</div>
        </td>
        <td style="border: none; width: 50%; text-align: center; vertical-align: top;">
            <div>Dosen Pembimbing</div>
            <div style="height: 60px;">
                @if(isset($signatures['supervisor']) && $signatures['supervisor'])
                    <img src="{{ $signatures['supervisor'] }}" style="max-height: 55px;">
                @endif
            </div>
            <div>{{ $supervisor->lecturer->name ?? '' }}</div>
            <div>NIDN &nbsp; {{ $supervisor->lecturer->nip ?? '' }}</div>
        </td>
    </tr>
</table>
@endsection
