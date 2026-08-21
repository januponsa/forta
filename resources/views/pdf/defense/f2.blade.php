@extends('pdf.defense.layout')

@section('title', 'F2 - Rekap Nilai')

@section('content')
<div style="text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline; margin: 10px 0 0 0;">
    REKAPITULASI
</div>
<div class="document-subtitle" style="margin-top: 5px;">
    NILAI UJIAN LAPORAN KERJA PRAKTIK
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

    // Get raw scores
    $np1_raw = $exmAssessment->total_score ?? 0;
    $npem_raw = $spvAssessment->total_score ?? 0;
    
    // For mentor
    $np2_raw = 0;
    if (isset($menAssessment)) {
        $menScores = collect($menAssessment->scores)->pluck('score');
        if ($menScores->count() > 0) {
            $np2_raw = $menScores->sum() / $menScores->count();
        }
    }
    
    $np1_display = round($np1_raw);
    $np2_display = round($np2_raw);
    $npem_display = round($npem_raw);
    
    $final_raw = ($np1_raw * 0.3) + ($np2_raw * 0.4) + ($npem_raw * 0.3);
    $final_display = round($final_raw);
    
    // Huruf Mutu
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

<table class="info-table" style="margin-bottom: 15px;">
    <tr>
        <td class="info-label">Nama Mahasiswa</td>
        <td class="info-colon">:</td>
        <td class="info-value">{{ $student->name }}</td>
    </tr>
    <tr>
        <td class="info-label">NIM</td>
        <td class="info-colon">:</td>
        <td class="info-value">{{ $student->nim }}</td>
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
    <tr>
        <td>Pembimbing</td>
        <td>:</td>
        <td>{{ $supervisor->lecturer->name ?? '' }}</td>
    </tr>
</table>

<table class="table-data" style="margin-bottom: 10px; width: 100%;">
    <thead>
        <tr>
            <th colspan="3" style="text-align: center; padding: 0; border: none; height: 0;"></th>
        </tr>
        <tr>
            <th style="text-align: center; width: 20%;">Nilai Penguji</th>
            <th style="text-align: center; width: 20%;">Nilai Mentor</th>
            <th style="text-align: center; width: 20%;">Nilai Pembimbing</th>
            <th colspan="2" style="text-align: center;">Nilai Akhir</th>
        </tr>
        <tr>
            <th style="text-align: center;">NP1</th>
            <th style="text-align: center;">NP2</th>
            <th style="text-align: center;">NPem</th>
            <th style="text-align: center; width: 20%;">Angka Mutu</th>
            <th style="text-align: center; width: 20%;">Huruf Mutu</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="text-align: center; font-size: 12pt; font-weight: bold;">{{ $np1_display }}</td>
            <td style="text-align: center; font-size: 12pt; font-weight: bold;">{{ $np2_display }}</td>
            <td style="text-align: center; font-size: 12pt; font-weight: bold;">{{ $npem_display }}</td>
            <td style="text-align: center; font-size: 12pt; font-weight: bold;">{{ $final_display }}</td>
            <td style="text-align: center; font-size: 14pt; font-weight: bold;">{{ $huruf_mutu }}</td>
        </tr>
    </tbody>
</table>

<table style="border: none; width: 100%; margin-top: 5px;">
    <tr>
        <td style="border: none; width: 55%; vertical-align: top;">
            <div class="keterangan">
                <strong>Keterangan:</strong><br>
                <em>Nilai Akhir = (NP1*30%+NP2*40%+NPem*30%)</em><br>
                <em>NP1 = Nilai penguji</em><br>
                <em>NP2 = Nilai Mentor</em><br>
                <em>NPem = Nilai Pembimbing</em>
            </div>
        </td>
        <td style="border: none; width: 45%; text-align: right; vertical-align: top;">
            <div>Tangerang, &nbsp; {{ $tanggal }} &nbsp; {{ $bulan }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $tahun }}</div>
            <div>Ketua Penguji,</div>
            <div style="height: 60px;">
                @if(isset($signatures['examiner']) && $signatures['examiner'])
                    <img src="{{ $signatures['examiner'] }}" style="max-height: 55px;">
                @endif
            </div>
            <div>{{ $examiner->lecturer->name ?? '' }}</div>
            <div>NIDN &nbsp;&nbsp; {{ $examiner->lecturer->nip ?? '' }}</div>
        </td>
    </tr>
</table>
@endsection
