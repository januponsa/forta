@extends('pdf.defense.layout')

@section('title', 'F1 - Berita Acara')

@section('content')
<div class="document-title">
    BERITA ACARA UJIAN LAPORAN KERJA PRAKTIK
</div>

@php
    $student = $case->student;
    $supervisor = $case->assignments->where('role', 'supervisor')->first();
    $examiner = $case->assignments->where('role', 'examiner')->first();
    $mentor = $case->metadata['mentor_name'] ?? '';
    $schedule = $case->latestSchedule;
    
    // Process Schedule
    $hari = '';
    $tanggal = '';
    $bulan = '';
    $tahun = '';
    $jam = '';
    
    if ($schedule && $schedule->scheduled_at) {
        $dt = \Carbon\Carbon::parse($schedule->scheduled_at)->locale('id');
        $hari = $dt->isoFormat('dddd');
        $tanggal = $dt->isoFormat('D');
        $bulan = $dt->isoFormat('MMMM');
        $tahun = $dt->isoFormat('YYYY');
        $jamEnd = \Carbon\Carbon::parse($schedule->scheduled_at)->addMinutes($schedule->duration_minutes ?? 30)->format('H.i');
        $jam = $dt->format('H.i') . ' - ' . $jamEnd;
    }

    // Huruf mutu
    $hurufMutu = '';
    $finalGrade = $case->final_grade ?? null;
    if ($finalGrade !== null) {
        if ($finalGrade >= 90) $hurufMutu = 'A';
        elseif ($finalGrade >= 85) $hurufMutu = 'A-';
        elseif ($finalGrade >= 80) $hurufMutu = 'B+';
        elseif ($finalGrade >= 75) $hurufMutu = 'B';
        elseif ($finalGrade >= 70) $hurufMutu = 'B-';
        elseif ($finalGrade >= 65) $hurufMutu = 'C+';
        elseif ($finalGrade >= 60) $hurufMutu = 'C';
        elseif ($finalGrade >= 50) $hurufMutu = 'D';
        else $hurufMutu = 'E';
    }

    $keputusan = 'LULUS / TIDAK LULUS *';
    if ($case->status === 'passed') $keputusan = 'LULUS';
    elseif ($case->status === 'passed_with_revision') $keputusan = 'LULUS';
    elseif ($case->status === 'failed') $keputusan = 'TIDAK LULUS';
@endphp

<p style="text-align: justify;">
    Pada hari ini, {{ $hari }} &nbsp; Tanggal &nbsp; {{ $tanggal }} &nbsp;, bulan &nbsp; {{ $bulan }} &nbsp;, tahun &nbsp; {{ $tahun }} &nbsp; pukul, {{ $jam }}<br>
    telah diadakan Sidang Laporan Kerja Praktik untuk saudara :
</p>

<table class="info-table" style="margin: 10px 0 15px 20px; width: 95%;">
    <tr>
        <td style="width: 28%;">Nama mahasiswa</td>
        <td style="width: 2%;">:</td>
        <td style="width: 70%;">{{ $student->name }}</td>
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

<p>Bertindak sebagai pelaksana sidang :</p>

<table class="info-table" style="margin: 5px 0 15px 20px; width: 95%;">
    <tr>
        <td style="width: 5%;">1.</td>
        <td style="width: 25%;">Ketua/Penguji</td>
        <td style="width: 2%;">:</td>
        <td style="width: 68%;">{{ $examiner->lecturer->name ?? '' }}</td>
    </tr>
    <tr><td colspan="4" style="height: 3px;"></td></tr>
    <tr>
        <td>2.</td>
        <td>Mentor</td>
        <td>:</td>
        <td>{{ $mentor }}</td>
    </tr>
    <tr><td colspan="4" style="height: 3px;"></td></tr>
    <tr>
        <td>3.</td>
        <td>Pembimbing</td>
        <td>:</td>
        <td>{{ $supervisor->lecturer->name ?? '' }}</td>
    </tr>
</table>

<p style="text-align: justify;">
    Hasil ujian laporan kerja praktik adalah mahasiswa dinyatakan <strong>{{ $keputusan }} *)</strong><br>
    dengan nilai &nbsp; <strong>{{ $hurufMutu }}</strong>
</p>

<p style="text-align: justify;">
    Mahasiswa bersangkutan diwajibkan untuk menyelesaikan perbaikan/revisi laporan kerja praktik
    dalam waktu selambat-lambatnya 10 hari kerja, terhitung sejak tanggal dilaksanakannya
    ujian laporan kerja praktik ini.<br>
    Lewat dari batas waktu yang telah ditentukan, hasil ujian laporan kerja praktik otomatis digugurkan.
</p>

<table style="border: none; width: 100%; margin-top: 30px;">
    <tr>
        <td style="border: none; width: 50%; text-align: left; vertical-align: top;">
            <div>Mahasiswa Ybs.,</div>
            <div style="height: 60px;"></div>
            <div>{{ $student->name }}</div>
            <div>NIM &nbsp; {{ $student->nim }}</div>
        </td>
        <td style="border: none; width: 50%; text-align: right; vertical-align: top;">
            <div>Tangerang, &nbsp; {{ $tanggal }} &nbsp; {{ $bulan }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $tahun }}</div>
            <div>Ketua Sidang,</div>
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
