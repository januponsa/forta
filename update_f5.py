import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\f5.blade.php'

content = """@extends('pdf.defense.layout')

@section('title', 'F5 - Evaluasi Mentor')

@section('content')
<div class="document-title">
    HASIL EVALUASI MENTOR
</div>

@php
    $student = $case->student;
    $mentor = $case->metadata['mentor_name'] ?? '-';
    $mentorNip = $case->metadata['mentor_nip'] ?? '-';
    $company = $case->metadata['company_name'] ?? '-';
    
    // Process rubrics
    $sections = [];
    $totalRaw = 0;
    $itemCount = 0;
    $fSectionPresent = false;
    
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
                    
                    if (str_starts_with($item->code, 'F')) {
                        $fSectionPresent = true;
                    } else {
                        // Normally A1 to E2 are used for the average
                        $totalRaw += $score->score;
                        $itemCount++;
                    }
                }
            }
        }
    }
    
    $average = $itemCount > 0 ? $totalRaw / $itemCount : 0;
    $averageDisplay = rtrim(rtrim(number_format($average, 2, '.', ''), '0'), '.');
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
    <tr>
        <td style="border: none; padding: 2px 0; vertical-align: top;">Nama Mentor</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">:</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">{{ $mentor }}</td>
    </tr>
    <tr>
        <td style="border: none; padding: 2px 0; vertical-align: top;">NIP Mentor</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">:</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">{{ $mentorNip }}</td>
    </tr>
    <tr>
        <td style="border: none; padding: 2px 0; vertical-align: top;">Nama Perusahaan</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">:</td>
        <td style="border: none; padding: 2px 0; vertical-align: top;">{{ $company }}</td>
    </tr>
</table>

<table class="table-data" style="margin-bottom: 20px;">
    <thead>
        <tr>
            <th style="text-align: left;">Penilaian</th>
            <th style="width: 20%; text-align: center;">Bobot (Jangkauan Nilai)</th>
            <th style="width: 15%; text-align: center;">Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sections as $secId => $sectionData)
            <tr>
                <td colspan="3" style="font-weight: bold; background-color: #eaeaea;">{{ $sectionData['name'] }}</td>
            </tr>
            @foreach($sectionData['items'] as $item)
                <tr>
                    <td style="text-align: justify;"><strong>{{ $item['code'] }} &mdash;</strong> {{ $item['description'] }}</td>
                    <td style="text-align: center;">1 &ndash; {{ $item['max'] }}</td>
                    <td style="text-align: center;">{{ $item['score'] }}</td>
                </tr>
            @endforeach
        @endforeach
        
        @if(!$fSectionPresent)
            <tr>
                <td colspan="3" style="font-weight: bold; background-color: #eaeaea;">F. Kompetensi Kejuruan</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; font-style: italic;">Tidak dinilai pada skema ini</td>
            </tr>
        @endif
        
        <tr>
            <td colspan="3" style="padding: 0; border: none; height: 10px;"></td>
        </tr>
        
        <tr>
            <td colspan="2" style="text-align: right; font-weight: bold;">Rata-Rata Nilai Mentor (A1 &ndash; E2) / 9</td>
            <td style="text-align: center; font-weight: bold; background-color: #f9f9f9; font-size: 12pt;">{{ number_format($average, 8, '.', '') }}</td>
        </tr>
    </tbody>
</table>

<div style="font-size: 9pt; line-height: 1.4; margin-bottom: 20px; font-style: italic; color: #555; text-align: justify;">
    * Nilai diinput dan diverifikasi oleh admin berdasarkan dokumen penilaian mentor yang dilampirkan.<br>
    Admin Verifikator: {{ $case->finalized_by ?? 'Sistem Akademik' }} | Waktu Input: {{ $assessment->updated_at ? $assessment->updated_at->format('d/m/Y H:i') : '-' }} | ID Dokumen Sumber: {{ $case->submission_id ?? '-' }}
</div>

<div class="signature-block">
    <div class="signature-left">
    </div>
    <div class="signature-right">
        <div>Tangerang, {{ $signatures['date'] ?? '-' }}</div>
        <div>Mentor,</div>
        <div class="signature-img" style="font-size: 9pt; color: #777; font-style: italic; padding: 15px 0;">
            (Tanda tangan asli terdapat<br>pada dokumen sumber lampiran)
        </div>
        <div style="font-weight: bold; text-decoration: underline;">{{ $mentor }}</div>
        <div>NIP {{ $mentorNip }}</div>
    </div>
    <div class="clear"></div>
</div>
@endsection
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
