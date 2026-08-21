@extends('pdf.defense.layout')

@section('title', 'F3 - Evaluasi Pembimbing')

@section('content')
<div class="document-title">
    HASIL EVALUASI PEMBIMBING
</div>

@php
    $student = $case->student;
    $supervisor = $case->assignments->where('role', 'supervisor')->first();
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

    // Process rubrics - group by section
    $sectionA = ['items' => [], 'subtotal' => 0]; // Ketekunan Mahasiswa
    $sectionB = ['items' => [], 'subtotal' => 0]; // Isi Laporan
    $total = 0;
    
    if (isset($assessment)) {
        foreach ($assessment->scores as $score) {
            $item = $score->rubricItem;
            if ($item) {
                $entry = [
                    'code' => $item->code,
                    'description' => $item->description,
                    'max' => $item->max_score,
                    'score' => $score->score,
                ];
                
                if (str_starts_with($item->code, 'A')) {
                    $sectionA['items'][] = $entry;
                    $sectionA['subtotal'] += $score->score;
                } elseif (str_starts_with($item->code, 'B')) {
                    $sectionB['items'][] = $entry;
                    $sectionB['subtotal'] += $score->score;
                }
                $total += $score->score;
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
            <th style="text-align: center;">Hal yang Dinilai</th>
            <th style="text-align: center; width: 18%;">Bobot<br>(Jangkauan Nilai)</th>
            <th style="text-align: center; width: 15%;">PEROLEHAN<br>NILAI</th>
        </tr>
    </thead>
    <tbody>
        {{-- Section A: Ketekunan Mahasiswa --}}
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">A. Ketekunan Mahasiswa</td>
        </tr>
        @foreach($sectionA['items'] as $item)
        <tr>
            <td style="text-align: justify;">{{ $item['description'] }} ({{ $item['code'] }})</td>
            <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $item['score'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">Nilai Ketekunan Mahasiswa NA = ( A1+A2)</td>
            <td style="text-align: center; font-weight: bold;">{{ $sectionA['subtotal'] }}</td>
        </tr>

        {{-- Section B: Isi Laporan --}}
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">B. Isi Laporan</td>
        </tr>
        @foreach($sectionB['items'] as $item)
        <tr>
            <td style="text-align: justify;">{{ $item['description'] }} ({{ $item['code'] }})</td>
            <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $item['score'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">Nilai Isi Laporan &nbsp; NB = ( B1+B2+B3)</td>
            <td style="text-align: center; font-weight: bold;">{{ $sectionB['subtotal'] }}</td>
        </tr>

        {{-- Total --}}
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">NILAI TOTAL ( NA+NB)</td>
            <td style="text-align: center; font-weight: bold; font-size: 12pt;">{{ $total }}</td>
        </tr>
    </tbody>
</table>

<table style="border: none; width: 100%; margin-top: 20px;">
    <tr>
        <td style="border: none; width: 50%;"></td>
        <td style="border: none; width: 50%; text-align: right;">
            <div>Tangerang, &nbsp; {{ $tanggal }} &nbsp; {{ $bulan }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $tahun }}</div>
            <div>Pembimbing,</div>
            <div style="height: 60px;">
                @if(isset($signatures['supervisor']) && $signatures['supervisor'])
                    <img src="{{ $signatures['supervisor'] }}" style="max-height: 55px;">
                @endif
            </div>
            <div>{{ $supervisor->lecturer->name ?? '' }}</div>
            <div>NIDN &nbsp;&nbsp; {{ $supervisor->lecturer->nip ?? '' }}</div>
        </td>
    </tr>
</table>
@endsection
