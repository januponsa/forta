import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\biodata.blade.php'

content = """@extends('pdf.defense.layout')

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
    $mentor = $case->metadata['mentor_name'] ?? '-';
    $mentorNip = $case->metadata['mentor_nip'] ?? '-';
    $company = $case->metadata['company_name'] ?? '-';
    
    // Process Schedule
    $hari = '-';
    $tanggal = '-';
    $bulan = '-';
    $tahun = '-';
    $jam = '-';
    
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

<table style="border: none; width: 100%; font-size: 11pt; line-height: 1.8;">
    <tr>
        <td style="width: 5%; border: none; vertical-align: top;">1.</td>
        <td style="width: 30%; border: none; vertical-align: top;">Nama Mahasiswa</td>
        <td style="width: 2%; border: none; vertical-align: top;">:</td>
        <td style="width: 63%; border: none; vertical-align: top;">{{ $student->name }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;">2.</td>
        <td style="border: none; vertical-align: top;">NIM</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $student->nim }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;">3.</td>
        <td style="border: none; vertical-align: top;">Program Studi</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">Informatika</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;">4.</td>
        <td style="border: none; vertical-align: top;">Semester</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $student->semester ?? '-' }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;">5.</td>
        <td style="border: none; vertical-align: top;">Judul Laporan Kerja Praktik</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $case->submission->title ?? '-' }}</td>
    </tr>
    
    <tr><td colspan="4" style="border: none; height: 10px;"></td></tr>
    
    <tr>
        <td style="border: none; vertical-align: top;">6.</td>
        <td style="border: none; vertical-align: top;">Nama Pembimbing</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $supervisor->lecturer->name ?? '-' }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;">7.</td>
        <td style="border: none; vertical-align: top;">NIDN</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $supervisor->lecturer->nip ?? '-' }}</td>
    </tr>
    
    <tr><td colspan="4" style="border: none; height: 10px;"></td></tr>
    
    <tr>
        <td style="border: none; vertical-align: top;">8.</td>
        <td style="border: none; vertical-align: top;">Nama Penguji</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $examiner->lecturer->name ?? '-' }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;">9.</td>
        <td style="border: none; vertical-align: top;">NIDN</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $examiner->lecturer->nip ?? '-' }}</td>
    </tr>

    <tr><td colspan="4" style="border: none; height: 10px;"></td></tr>
    
    <tr>
        <td style="border: none; vertical-align: top;">10.</td>
        <td style="border: none; vertical-align: top;">Nama Mentor</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $mentor }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;">11.</td>
        <td style="border: none; vertical-align: top;">NIP Mentor</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $mentorNip }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;"></td>
        <td style="border: none; vertical-align: top;">Nama Perusahaan</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $company }}</td>
    </tr>

    <tr><td colspan="4" style="border: none; height: 10px;"></td></tr>
    
    <tr>
        <td style="border: none; vertical-align: top;">12.</td>
        <td colspan="3" style="border: none; vertical-align: top;">Pelaksanaan Sidang</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;"></td>
        <td style="border: none; vertical-align: top; padding-left: 15px;">- Hari</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $hari }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;"></td>
        <td style="border: none; vertical-align: top; padding-left: 15px;">- Tanggal</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $tanggal }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;"></td>
        <td style="border: none; vertical-align: top; padding-left: 15px;">- Bulan</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $bulan }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;"></td>
        <td style="border: none; vertical-align: top; padding-left: 15px;">- Tahun</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $tahun }}</td>
    </tr>
    <tr>
        <td style="border: none; vertical-align: top;"></td>
        <td style="border: none; vertical-align: top; padding-left: 15px;">- Jam</td>
        <td style="border: none; vertical-align: top;">:</td>
        <td style="border: none; vertical-align: top;">{{ $jam }}</td>
    </tr>
</table>
@endsection
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
