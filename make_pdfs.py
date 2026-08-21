import os

base_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense'

templates = {
    'biodata': """@extends('pdf.defense.layout')
@section('title', 'Biodata Sidang')
@section('content')
<div class="document-title">BIODATA PESERTA SIDANG MAGANG/KERJA PRAKTIK</div>
<div class="row"><div class="col-4">Nama</div><div class="col-8">: {{ $case->student->name }}</div></div>
<div class="row"><div class="col-4">NIM</div><div class="col-8">: {{ $case->student->nim }}</div></div>
<div class="row"><div class="col-4">Program Studi</div><div class="col-8">: {{ $case->student->program_studi ?? 'Sistem Informasi' }}</div></div>
<div class="row"><div class="col-4">Judul Laporan</div><div class="col-8">: {{ $case->metadata['report_title'] ?? '-' }}</div></div>
<div class="row"><div class="col-4">Perusahaan</div><div class="col-8">: {{ $case->metadata['company_name'] ?? '-' }}</div></div>
<div class="clear"></div>
@endsection
""",
    'f1': """@extends('pdf.defense.layout')
@section('title', 'F1 - Berita Acara')
@section('content')
<div class="document-title">F1 - BERITA ACARA SIDANG MAGANG/KERJA PRAKTIK</div>
<p>Pada hari ini {{ $schedule ? $schedule->date->format('l') : '-' }} tanggal {{ $schedule ? $schedule->date->format('d F Y') : '-' }}, telah dilaksanakan Sidang Magang/Kerja Praktik untuk mahasiswa:</p>
<div class="row"><div class="col-4">Nama</div><div class="col-8">: {{ $case->student->name }}</div></div>
<div class="row"><div class="col-4">NIM</div><div class="col-8">: {{ $case->student->nim }}</div></div>
<div class="clear"></div>
<p>Berdasarkan hasil penilaian, mahasiswa tersebut dinyatakan: <strong>{{ strtoupper(str_replace('_', ' ', $case->status)) }}</strong> dengan Nilai Huruf <strong>{{ $case->final_grade }}</strong>.</p>
<div class="signature-block">
    <div class="signature-left">Dosen Pembimbing<br><br><br>({{ $supervisorName }})</div>
    <div class="signature-right">Dosen Penguji<br><br><br>({{ $examinerName }})</div>
</div>
<div class="clear"></div>
@endsection
""",
    'f2': """@extends('pdf.defense.layout')
@section('title', 'F2 - Rekap Nilai')
@section('content')
<div class="document-title">F2 - REKAPITULASI NILAI SIDANG</div>
<table class="table-data">
    <tr><th>Komponen</th><th>Bobot</th><th>Nilai</th><th>Total</th></tr>
    <tr><td>Dosen Penguji</td><td>30%</td><td>{{ $examinerScore }}</td><td>{{ $examinerScore * 0.3 }}</td></tr>
    <tr><td>Mentor Lapangan</td><td>40%</td><td>{{ $mentorScore }}</td><td>{{ $mentorScore * 0.4 }}</td></tr>
    <tr><td>Dosen Pembimbing</td><td>30%</td><td>{{ $supervisorScore }}</td><td>{{ $supervisorScore * 0.3 }}</td></tr>
    <tr><th colspan="3">NILAI AKHIR</th><th>{{ $case->final_score }} ({{ $case->final_grade }})</th></tr>
</table>
@endsection
""",
    'f3': """@extends('pdf.defense.layout')
@section('title', 'F3 - Penilaian Pembimbing')
@section('content')
<div class="document-title">F3 - PENILAIAN DOSEN PEMBIMBING</div>
<div class="row"><div class="col-4">Nama Mahasiswa</div><div class="col-8">: {{ $case->student->name }}</div></div>
<div class="clear"></div><br>
<table class="table-data">
    <tr><th>Indikator</th><th>Nilai</th></tr>
    @foreach($assessment->scores as $score)
    <tr><td>{{ $score->rubricItem->name }}</td><td style="text-align: center;">{{ $score->score }}</td></tr>
    @endforeach
    <tr><th>Total Nilai Pembimbing</th><th style="text-align: center;">{{ $assessment->total_score }}</th></tr>
</table>
<div class="signature-block">
    <div class="signature-right">Pembimbing<br><br><br>({{ $assessment->lecturer->name ?? '-' }})</div>
</div>
<div class="clear"></div>
@endsection
""",
    'f4': """@extends('pdf.defense.layout')
@section('title', 'F4 - Penilaian Penguji')
@section('content')
<div class="document-title">F4 - PENILAIAN DOSEN PENGUJI</div>
<div class="row"><div class="col-4">Nama Mahasiswa</div><div class="col-8">: {{ $case->student->name }}</div></div>
<div class="clear"></div><br>
<table class="table-data">
    <tr><th>Indikator</th><th>Nilai</th></tr>
    @foreach($assessment->scores as $score)
    <tr><td>{{ $score->rubricItem->name }}</td><td style="text-align: center;">{{ $score->score }}</td></tr>
    @endforeach
    <tr><th>Total Nilai Penguji</th><th style="text-align: center;">{{ $assessment->total_score }}</th></tr>
</table>
<p>Catatan Originalitas: <strong>{{ $assessment->notes ?? '-' }}</strong></p>
<div class="signature-block">
    <div class="signature-right">Penguji<br><br><br>({{ $assessment->lecturer->name ?? '-' }})</div>
</div>
<div class="clear"></div>
@endsection
""",
    'f5': """@extends('pdf.defense.layout')
@section('title', 'F5 - Penilaian Mentor')
@section('content')
<div class="document-title">F5 - HASIL INPUT PENILAIAN MENTOR</div>
<div class="row"><div class="col-4">Nama Mahasiswa</div><div class="col-8">: {{ $case->student->name }}</div></div>
<div class="row"><div class="col-4">Nama Mentor</div><div class="col-8">: {{ $case->metadata['mentor_name'] ?? '-' }}</div></div>
<div class="clear"></div><br>
<table class="table-data">
    <tr><th>Indikator</th><th>Nilai</th></tr>
    @foreach($assessment->scores as $score)
    <tr><td>{{ $score->rubricItem->name }}</td><td style="text-align: center;">{{ $score->score }}</td></tr>
    @endforeach
    <tr><th>Total Rata-rata Mentor</th><th style="text-align: center;">{{ $assessment->total_score }}</th></tr>
</table>
<p><em>Diinput dan diverifikasi berdasarkan dokumen lembar penilaian mentor yang dilampirkan.</em></p>
@endsection
""",
    'f6': """@extends('pdf.defense.layout')
@section('title', 'F6 - Saran & Perbaikan')
@section('content')
<div class="document-title">F6 - DAFTAR SARAN & PERBAIKAN SIDANG</div>
<div class="row"><div class="col-4">Nama Mahasiswa</div><div class="col-8">: {{ $case->student->name }}</div></div>
<div class="clear"></div><br>
<table class="table-data">
    <tr><th>No</th><th>Saran / Perbaikan</th><th>Dosen Pemberi</th><th>Status</th></tr>
    @foreach($suggestions as $idx => $sug)
    <tr>
        <td style="text-align: center;">{{ $idx + 1 }}</td>
        <td><strong>{{ $sug->category }}</strong><br>{{ $sug->suggestion }}</td>
        <td>{{ $sug->lecturer->name }}<br><small>({{ $sug->role }})</small></td>
        <td>{{ $sug->status }}</td>
    </tr>
    @endforeach
</table>
@endsection
"""
}

for name, content in templates.items():
    with open(os.path.join(base_path, f'{name}.blade.php'), 'w', encoding='utf-8') as f:
        f.write(content)

print("PDF templates generated.")
