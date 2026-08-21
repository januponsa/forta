import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\f1.blade.php'

content = """@extends('pdf.defense.layout')

@section('title', 'F1 - Berita Acara')

@section('content')
<div class="document-title">
    BERITA ACARA UJIAN LAPORAN KERJA PRAKTIK
</div>

@php
    $student = $case->student;
    $mentor = $case->metadata['mentor_name'] ?? '-';
    
    // Process Schedule
    $hari = '-';
    $tanggal = '-';
    $bulan = '-';
    $tahun = '-';
    $jam = '-';
    
    if (isset($schedule) && $schedule->scheduled_at) {
        $dt = \Carbon\Carbon::parse($schedule->scheduled_at)->locale('id');
        $hari = $dt->isoFormat('dddd');
        $tanggal = $dt->isoFormat('D');
        $bulan = $dt->isoFormat('MMMM');
        $tahun = $dt->isoFormat('YYYY');
        $jamEnd = \Carbon\Carbon::parse($schedule->scheduled_at)->addMinutes($schedule->duration_minutes ?? 30)->format('H.i');
        $jam = $dt->format('H.i') . ' - ' . $jamEnd;
    }

    $keputusan = '-';
    if ($case->status === 'passed') $keputusan = 'LULUS';
    elseif ($case->status === 'passed_with_revision') $keputusan = 'LULUS DENGAN REVISI';
    elseif ($case->status === 'failed') $keputusan = 'TIDAK LULUS';
@endphp

<p style="text-align: justify;">
    Pada hari ini, {{ $hari }}, tanggal {{ $tanggal }}, bulan {{ $bulan }}, tahun {{ $tahun }}, pukul {{ $jam }}, telah diadakan Sidang Laporan Kerja Praktik untuk mahasiswa:
</p>

<table style="border: none; width: 100%; margin: 15px 0;">
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

<p>Pelaksana Sidang Laporan Kerja Praktik adalah sebagai berikut:</p>
<table style="border: none; width: 100%; margin: 10px 0;">
    <tr>
        <td style="width: 5%; border: none;">1.</td>
        <td style="width: 30%; border: none;">Ketua/Penguji</td>
        <td style="width: 2%; border: none;">:</td>
        <td style="width: 63%; border: none;">{{ $examinerName }}</td>
    </tr>
    <tr>
        <td style="border: none;">2.</td>
        <td style="border: none;">Mentor</td>
        <td style="border: none;">:</td>
        <td style="border: none;">{{ $mentor }}</td>
    </tr>
    <tr>
        <td style="border: none;">3.</td>
        <td style="border: none;">Pembimbing</td>
        <td style="border: none;">:</td>
        <td style="border: none;">{{ $supervisorName }}</td>
    </tr>
</table>

<p>Berdasarkan hasil ujian, mahasiswa dinyatakan:</p>
<div style="text-align: center; font-size: 14pt; font-weight: bold; margin: 20px 0;">
    {{ $keputusan }}<br>
    <span style="font-size: 11pt; font-weight: normal;">dengan nilai {{ $case->final_grade ?? '-' }}</span>
</div>

<div style="text-align: justify; margin: 20px 0;">
    Mahasiswa yang bersangkutan diwajibkan untuk menyelesaikan perbaikan/revisi laporan kerja praktik dalam waktu selambat-lambatnya 10 hari kerja, terhitung sejak tanggal dilaksanakannya ujian laporan kerja praktik ini.<br><br>
    Lewat dari batas waktu yang telah ditentukan, hasil ujian laporan kerja praktik otomatis digugurkan.
</div>

<div class="signature-block">
    <div class="signature-left">
        <div>Mahasiswa Ybs.</div>
        <div class="signature-img">
            <div style="margin-top: 25px; font-style: italic; color: #555; font-size: 9pt;">Disetujui via sistem</div>
        </div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $student->name }}</div>
        <div>NIM {{ $student->nim }}</div>
    </div>
    
    <div class="signature-right">
        <div>Tangerang, {{ $signatures['date'] ?? '-' }}</div>
        <div>Ketua Sidang</div>
        <div class="signature-img">
            @if(isset($signatures['examiner']) && $signatures['examiner'])
                <img src="{{ $signatures['examiner'] }}" style="max-height: 60px;">
            @endif
        </div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $examinerName }}</div>
        <div>NIDN {{ $case->assignments->where('role', 'examiner')->first()->lecturer->nip ?? '-' }}</div>
    </div>
    <div class="clear"></div>
</div>
@endsection
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
