@extends('pdf.defense.layout')

@section('title', 'Biodata Sidang')

@section('content')
<div class="document-title">
    BIODATA UJIAN LAPORAN KERJA PRAKTIK
</div>

@php
    $student = $case->student;
    $supervisor = $case->assignments->where('role', 'supervisor')->first();
    $examiner = $case->assignments->where('role', 'examiner')->first();
    $schedule = $case->latestSchedule;
    $mentor = $case->metadata['mentor_name'] ?? '';
    $mentorNip = $case->metadata['mentor_nip'] ?? '';
    
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
@endphp

<table class="info-table" style="margin-bottom: 5px;">
    <tr>
        <td style="width: 5%;">1.</td>
        <td style="width: 28%;">Nama Mahasiswa</td>
        <td style="width: 2%;">:</td>
        <td style="width: 65%;">{{ $student->name }}</td>
    </tr>
    <tr>
        <td>2.</td>
        <td>NIM</td>
        <td>:</td>
        <td>{{ $student->nim }}</td>
    </tr>
    <tr>
        <td>3.</td>
        <td>Program Studi</td>
        <td>:</td>
        <td>Informatika</td>
    </tr>
    <tr>
        <td>4.</td>
        <td>Semester</td>
        <td>:</td>
        <td>{{ $student->semester ?? '' }}</td>
    </tr>
    <tr>
        <td>5.</td>
        <td style="vertical-align: top;">Judul Laporan Kerja Praktik</td>
        <td style="vertical-align: top;">:</td>
        <td style="vertical-align: top; text-transform: uppercase;">{{ $case->submission->title ?? '' }}</td>
    </tr>
</table>

<table class="info-table" style="margin-bottom: 5px;">
    <tr><td colspan="4" style="height: 8px;"></td></tr>
    <tr>
        <td style="width: 5%;">6.</td>
        <td style="width: 28%; font-weight: bold;">Nama Pembimbing</td>
        <td style="width: 2%;">:</td>
        <td style="width: 65%;">{{ $supervisor->lecturer->name ?? '' }}</td>
    </tr>
    <tr>
        <td>7.</td>
        <td style="font-weight: bold;">NIDN</td>
        <td>:</td>
        <td>{{ $supervisor->lecturer->nip ?? '' }}</td>
    </tr>
</table>

<table class="info-table" style="margin-bottom: 5px;">
    <tr><td colspan="4" style="height: 8px;"></td></tr>
    <tr>
        <td style="width: 5%;">8.</td>
        <td style="width: 28%; font-weight: bold;">Nama Penguji</td>
        <td style="width: 2%;">:</td>
        <td style="width: 65%;">{{ $examiner->lecturer->name ?? '' }}</td>
    </tr>
    <tr>
        <td>9.</td>
        <td style="font-weight: bold;">NIDN</td>
        <td>:</td>
        <td>{{ $examiner->lecturer->nip ?? '' }}</td>
    </tr>
</table>

<table class="info-table" style="margin-bottom: 5px;">
    <tr><td colspan="4" style="height: 8px;"></td></tr>
    <tr>
        <td style="width: 5%;">10.</td>
        <td style="width: 28%; font-weight: bold;">Nama Mentor</td>
        <td style="width: 2%;">:</td>
        <td style="width: 65%;">{{ $mentor }}</td>
    </tr>
    <tr>
        <td>11.</td>
        <td style="font-weight: bold;">NIP</td>
        <td>:</td>
        <td>{{ $mentorNip }}</td>
    </tr>
</table>

<table class="info-table">
    <tr><td colspan="4" style="height: 8px;"></td></tr>
    <tr>
        <td style="width: 5%;">12.</td>
        <td colspan="3" style="font-weight: bold;">Pelaksanaan Sidang</td>
    </tr>
    <tr>
        <td></td>
        <td style="width: 28%; padding-left: 20px;">- Hari</td>
        <td style="width: 2%;">:</td>
        <td style="width: 65%;">{{ $hari }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 20px;">- Tanggal</td>
        <td>:</td>
        <td>{{ $tanggal }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 20px;">- Bulan</td>
        <td>:</td>
        <td>{{ $bulan }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 20px;">- Tahun</td>
        <td>:</td>
        <td>{{ $tahun }}</td>
    </tr>
    <tr>
        <td></td>
        <td style="padding-left: 20px;">- Jam</td>
        <td>:</td>
        <td>{{ $jam }}</td>
    </tr>
</table>
@endsection
