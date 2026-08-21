import sys

file_path = r'c:\Users\userJ\Documents\fortain\resources\views\pdf\defense\layout.blade.php'

content = """<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dokumen Sidang')</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
            position: relative;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            padding: 0;
        }
        .header-logo {
            width: 80px;
            text-align: left;
            vertical-align: middle;
        }
        .header-logo img {
            max-width: 100px;
            max-height: 80px;
        }
        .header-text {
            text-align: center;
            vertical-align: middle;
        }
        .header h1 {
            font-size: 16pt;
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14pt;
            margin: 5px 0 0 0;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 9pt;
        }
        .document-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            text-decoration: underline;
        }
        .document-number {
            text-align: center;
            font-size: 10pt;
            margin-top: -15px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-data, .table-data th, .table-data td {
            border: 1px solid #000;
        }
        .table-data th, .table-data td {
            padding: 5px;
        }
        .table-data th {
            background-color: #f2f2f2;
        }
        .row {
            clear: both;
            margin-bottom: 10px;
        }
        .col-4 {
            width: 30%;
            float: left;
            font-weight: bold;
        }
        .col-8 {
            width: 70%;
            float: left;
        }
        .signature-block {
            margin-top: 40px;
            width: 100%;
        }
        .signature-left {
            width: 50%;
            float: left;
            text-align: center;
        }
        .signature-right {
            width: 50%;
            float: right;
            text-align: center;
        }
        .signature-img {
            height: 60px;
            margin: 10px 0;
        }
        .clear {
            clear: both;
        }
        .page-break {
            page-break-after: always;
        }
        .watermark {
            position: fixed;
            top: 40%;
            left: 20%;
            font-size: 80pt;
            color: rgba(200, 200, 200, 0.5);
            transform: rotate(-45deg);
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    @if(isset($isDraft) && $isDraft)
    <div class="watermark">DRAFT</div>
    @endif

    <div class="header">
        <table>
            <tr>
                <td class="header-logo">
                    @if(isset($logoData) && $logoData)
                        <img src="{{ $logoData }}" alt="Logo">
                    @endif
                </td>
                <td class="header-text">
                    <h1>{{ $template->university_name ?? 'UNIVERSITAS PRADITA' }}</h1>
                    <h2>FAKULTAS SAINS DAN TEKNOLOGI</h2>
                    <h2>PROGRAM STUDI INFORMATIKA</h2>
                    <p>{{ $template->campus_address ?? 'Kampus Utama - Menara Satu Kelapa Gading Lt. 11, Jalan Boulevard Raya LA 3 No.1, Klp. Gading, Jakarta Utara, 14240' }}</p>
                    <p>{{ $template->contact_info ?? '(021) 5568 9999 | www.pradita.ac.id' }}</p>
                </td>
            </tr>
        </table>
    </div>
    
    @yield('content')
    
</body>
</html>
"""

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)
