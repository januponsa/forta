<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pengantar Magang</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.25;
            color: #000;
            box-sizing: border-box;
            position: relative;
        }

        .container {
            padding: 12mm 25mm 20mm 25mm; /* Reduced top and bottom margins to fit in 1 page */
            height: 100%;
            box-sizing: border-box;
            position: relative;
        }

        .header {
            text-align: right;
            margin-bottom: 10mm;
        }

        .logo {
            max-height: 25mm;
            max-width: 70mm;
            height: auto;
            width: auto;
        }

        .meta {
            margin-bottom: 5mm;
        }
        
        .meta table {
            width: auto;
        }
        
        .meta td {
            vertical-align: top;
            padding: 0;
            line-height: 1.25;
        }

        .meta td:nth-child(1) { width: 15mm; }
        .meta td:nth-child(2) { width: 3mm; text-align: center; }

        .destination {
            margin-bottom: 5mm;
            line-height: 1.25;
        }

        .content {
            text-align: justify;
            margin-bottom: 5mm;
            line-height: 1.25;
        }
        
        .content p {
            margin: 0 0 6pt 0;
            text-align: justify;
        }

        .student-details {
            margin: 3mm 0;
        }

        .student-details table {
            width: 100%;
            margin-bottom: 3mm;
        }

        .student-details td {
            padding: 1mm 0;
            vertical-align: top;
        }

        .student-details td:nth-child(1) { width: 35mm; font-weight: bold; }
        .student-details td:nth-child(2) { width: 3mm; font-weight: bold; }
        .student-details td:nth-child(3) { font-weight: bold; }

        .signature-block {
            margin-top: 5mm;
            width: 100%;
            position: relative;
        }

        .signature-content {
            width: 80mm; /* align left side */
        }
        
        .signature-date {
            margin-bottom: 0mm;
        }

        .signature-image {
            height: 20mm;
            width: auto;
            margin-top: 1mm;
            margin-bottom: 1mm;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 0;
        }

        .signature-position {
            font-weight: bold;
            margin-top: 0;
        }

        .footer {
            position: absolute;
            bottom: 12mm;
            left: 0;
            right: 0;
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            line-height: 1.2;
        }
        
        .footer-title {
            font-size: 9pt;
            margin-bottom: 1mm;
        }
    </style>
</head>
<body>
    @php
        $logoPath = $template->logoAsset && $template->logoAsset->activeVersion && $template->logoAsset->activeVersion->original_path
            ? Storage::disk('public')->path($template->logoAsset->activeVersion->original_path)
            : Storage::disk('private')->path('letter-assets/logo.png'); // fallback
            
        $logoData = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $signaturePath = $template->lecturer && $template->lecturer->signature_path
            ? Storage::disk('public')->path($template->lecturer->signature_path)
            : Storage::disk('private')->path('letter-assets/signature.png'); // fallback
            
        $signatureData = file_exists($signaturePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath)) : '';
    @endphp

    <div class="container">
        <div class="header">
            @if($logoData)
                <img src="{{ $logoData }}" class="logo" alt="Logo">
            @else
                <div style="height: 20mm; width: 60mm; border: 1px dashed #ccc; display: inline-block; text-align: center; line-height: 20mm;">[Logo]</div>
            @endif
        </div>

        <div class="meta">
            <table border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td>{{ $letterNumber }}</td>
                </tr>
                <tr>
                    <td>Perihal</td>
                    <td>:</td>
                    <td><strong>{{ $template->subject ?? 'Surat Pengantar Kerja Praktik' }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="destination">
            <div style="font-weight: bold;">Kepada Yth.</div>
            <div style="font-weight: bold;">{{ $request->recipient_name }}</div>
            <div style="font-weight: bold;">{{ $request->company_name }}</div>
            <div style="width: 75%;">{{ $request->company_address }}</div>
            @if($request->company_city)
                <div>{{ $request->company_city }}</div>
            @endif
        </div>

        <div class="content">
            <p>{!! nl2br(e($opening)) !!}</p>

            <div class="student-details">
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>{{ $request->student->name }}</td>
                    </tr>
                    <tr>
                        <td>NIM</td>
                        <td>:</td>
                        <td>{{ $request->student->nim }}</td>
                    </tr>
                    <tr>
                        <td>Program Studi</td>
                        <td>:</td>
                        <td>Informatika</td>
                    </tr>
                    <tr>
                        <td>Semester</td>
                        <td>:</td>
                        <td>{{ $request->student->semester ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <p>{!! nl2br(e($purpose)) !!}</p>
            
            <p>{!! nl2br(e($closing)) !!}</p>
        </div>

        <div class="signature-block">
            <div class="signature-content">
                <div class="signature-date">Tangerang, {{ $date }}</div>
                
                @if($signatureData)
                    <img src="{{ $signatureData }}" class="signature-image" alt="Signature">
                @else
                    <div style="height: 25mm; width: 40mm; border: 1px dashed #ccc; margin-top: 2mm; margin-bottom: 2mm; text-align: center; line-height: 25mm;">[Tanda Tangan]</div>
                @endif
                
                <div class="signature-name">{{ $template->lecturer ? $template->lecturer->name : ($template->signatory_name ?? '(Erick Dazki, S.Kom., M.Kom)') }}</div>
                <div class="signature-position">{{ $template->lecturer ? $template->lecturer->position : ($template->signatory_position ?? 'Ketua Program Studi Informatika') }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-title">UNIVERSITAS PRADITA</div>
            <div>Kampus Utama - Menara Satu Kelapa Gading Lt. 11, Jalan Boulevard Raya LA 3 No.1, Klp. Gading, Jakarta Utara, 14240</div>
            <div>Kampus Serpong - Scientia Business Park, Jalan Boulevard Gading Serpong Blok O/1, Tangerang, 15810</div>
            <div>(021) 5568 9999 | www.pradita.ac.id</div>
        </div>
    </div>
</body>
</html>
