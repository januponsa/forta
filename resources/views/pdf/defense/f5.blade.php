@extends('pdf.defense.layout')

@section('title', 'F5 - Evaluasi Mentor')

@section('content')
<div class="document-title">
    HASIL EVALUASI MENTOR
</div>

@php
    $student = $case->student;
    $mentor = $case->metadata['mentor_name'] ?? '';
    $mentorNip = $case->metadata['mentor_nip'] ?? '';
    $company = $case->metadata['company_name'] ?? '';
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
    
    // Process rubrics
    $sections = [];
    $totalRaw = 0;
    $itemCount = 0;
    
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
                    $totalRaw += $score->score;
                    $itemCount++;
                }
            }
        }
    }
    
    $average = $itemCount > 0 ? $totalRaw / $itemCount : 0;
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
    <tr>
        <td>Nama Mentor</td>
        <td>:</td>
        <td>{{ $mentor }}</td>
    </tr>
    <tr>
        <td>NIP Mentor</td>
        <td>:</td>
        <td>{{ $mentorNip }}</td>
    </tr>
    <tr>
        <td>Nama Perusahaan</td>
        <td>:</td>
        <td>{{ $company }}</td>
    </tr>
</table>

<table class="table-data" style="margin-bottom: 15px; font-size: 10pt;">
    <thead>
        <tr>
            <th style="text-align: center;">Penilaian</th>
            <th style="text-align: center; width: 20%;">Bobot (Jangkauan Nilai)</th>
            <th style="text-align: center; width: 12%;">Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sections as $secId => $sectionData)
            <tr>
                <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">{{ $sectionData['name'] }}</td>
            </tr>
            @foreach($sectionData['items'] as $item)
            <tr>
                <td style="text-align: justify;">{{ $item['description'] }} ({{ $item['code'] }})</td>
                <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $item['score'] }}</td>
            </tr>
            @endforeach
        @endforeach
        
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">RATA-RATA NILAI MENTOR</td>
            <td style="text-align: center; font-weight: bold; font-size: 12pt;">{{ round($average) }}</td>
        </tr>
    </tbody>
</table>

<div style="font-size: 9pt; font-style: italic; color: #555; margin-bottom: 15px;">
    * Nilai diinput dan diverifikasi oleh admin berdasarkan dokumen penilaian mentor yang dilampirkan.
</div>

<table style="border: none; width: 100%; margin-top: 20px;">
    <tr>
        <td style="border: none; width: 50%;"></td>
        <td style="border: none; width: 50%; text-align: right;">
            <div>Tangerang, &nbsp; {{ $tanggal }} &nbsp; {{ $bulan }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $tahun }}</div>
            <div>Mentor,</div>
            <div style="height: 60px; font-size: 9pt; color: #777; font-style: italic; padding-top: 20px;">
                (Tanda tangan asli pada dokumen sumber)
            </div>
            <div>{{ $mentor }}</div>
            <div>NIP &nbsp;&nbsp; {{ $mentorNip }}</div>
        </td>
    </tr>
</table>
@endsection
