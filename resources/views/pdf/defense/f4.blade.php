@extends('pdf.defense.layout')

@section('title', 'F4 - Evaluasi Penguji')

@section('content')
<div class="document-title">
    HASIL EVALUASI PENGUJI -1 / KETUA
</div>

@php
    $student = $case->student;
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

    // Process rubrics - group by section
    $sectionA = ['items' => [], 'subtotal' => 0]; // Pemahaman Isi Laporan
    $sectionB = ['items' => [], 'subtotal' => 0]; // Laporan Kerja Praktik
    $sectionC = ['items' => [], 'subtotal' => 0]; // Sidang/Seminar Kerja Praktik
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
                } elseif (str_starts_with($item->code, 'C')) {
                    $sectionC['items'][] = $entry;
                    $sectionC['subtotal'] += $score->score;
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

<table class="table-data" style="margin-bottom: 10px; font-size: 10pt;">
    <thead>
        <tr>
            <th style="text-align: center;">Penilaian</th>
            <th style="text-align: center; width: 20%;">Bobot (Jangkauan<br>Nilai)</th>
            <th style="text-align: center; width: 12%;">Nilai</th>
        </tr>
    </thead>
    <tbody>
        {{-- Section A: Pemahaman Isi Laporan --}}
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">A. Pemahaman Isi Laporan</td>
        </tr>
        @foreach($sectionA['items'] as $item)
        <tr>
            <td style="text-align: justify;">{{ $item['description'] }} ({{ $item['code'] }})</td>
            <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $item['score'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">Nilai Pemahaman Isi Laporan, NA = ( A1+A2+A3)</td>
            <td style="text-align: center; font-weight: bold;">{{ $sectionA['subtotal'] }}</td>
        </tr>

        {{-- Section B: Laporan Kerja Praktik --}}
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">B. Laporan Kerja Praktik</td>
        </tr>
        @foreach($sectionB['items'] as $item)
        <tr>
            <td style="text-align: justify;">{{ $item['description'] }} ({{ $item['code'] }})</td>
            <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $item['score'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">Nilai Laporan Kerja Praktik, NB = ( B1+B2 )</td>
            <td style="text-align: center; font-weight: bold;">{{ $sectionB['subtotal'] }}</td>
        </tr>

        {{-- Section C: Sidang / Seminar Kerja Praktik --}}
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">C. Sidang / Seminar Kerja Praktik</td>
        </tr>
        @foreach($sectionC['items'] as $item)
        <tr>
            <td style="text-align: justify;">{{ $item['description'] }} ({{ $item['code'] }})</td>
            <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
            <td style="text-align: center; font-weight: bold;">{{ $item['score'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">Nilai Sidang / Seminar Kerja Praktik, NC =(C1+C2+C3)</td>
            <td style="text-align: center; font-weight: bold;">{{ $sectionC['subtotal'] }}</td>
        </tr>

        {{-- Section D: Originalitas --}}
        <tr>
            <td colspan="3" style="font-weight: bold; background-color: #dce6f1;">D. Originalitas</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: justify; font-size: 9pt;">
                Jika pada saat sidang kerja praktik mahasiswa yang diuji
                terbukti berisi plagiat, maka mahasiswa tersebut akan
                dinyatakan gagal (kolom nilai pada kertas penilaian diisi
                F)
            </td>
        </tr>

        {{-- Total --}}
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold; padding-right: 10px;">NILAI TOTAL ( NA+NB+NC)</td>
            <td style="text-align: center; font-weight: bold; font-size: 12pt;">{{ $total }}</td>
        </tr>
    </tbody>
</table>

<table style="border: none; width: 100%; margin-top: 20px;">
    <tr>
        <td style="border: none; width: 50%;"></td>
        <td style="border: none; width: 50%; text-align: right;">
            <div>Tangerang, &nbsp; {{ $tanggal }} &nbsp; {{ $bulan }} &nbsp;&nbsp;&nbsp;&nbsp; {{ $tahun }}</div>
            <div>Ketua/Penguji</div>
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
