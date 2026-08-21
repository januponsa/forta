<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dokumen Sidang')</title>
    <style>
        @font-face {
            font-family: 'Titillium Web';
            font-style: normal;
            font-weight: 400;
            src: url("{{ public_path('fonts/TitilliumWeb-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Titillium Web';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/TitilliumWeb-Bold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Titillium Web';
            font-style: italic;
            font-weight: 400;
            src: url("{{ public_path('fonts/TitilliumWeb-Italic.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Titillium Web';
            font-style: italic;
            font-weight: bold;
            src: url("{{ public_path('fonts/TitilliumWeb-BoldItalic.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Calibri';
            font-style: normal;
            font-weight: 400;
            src: url("{{ public_path('fonts/Carlito-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Calibri';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/Carlito-Bold.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Calibri';
            font-style: italic;
            font-weight: 400;
            src: url("{{ public_path('fonts/Carlito-Italic.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Calibri';
            font-style: italic;
            font-weight: bold;
            src: url("{{ public_path('fonts/Carlito-BoldItalic.ttf') }}") format('truetype');
        }

        @page {
            margin: 20mm 20mm 20mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            position: relative;
        }
        .header {
            width: 100%;
            margin-bottom: 5px;
            padding-bottom: 0;
        }
        .header table {
            width: 100%;
            border: none;
        }
        .header td {
            border: none;
            padding: 0;
            vertical-align: bottom;
        }
        .header-logo {
            width: 120px;
            text-align: left;
            vertical-align: bottom;
            padding-bottom: 5px;
        }
        .header-logo img {
            max-width: 120px;
            max-height: 55px;
        }
        .document-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 10px 0 20px 0;
        }
        .document-subtitle {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-data, .table-data th, .table-data td {
            border: 1px solid #000;
        }
        .table-data th {
            background-color: #b8cce4;
            padding: 5px 8px;
            font-weight: bold;
            text-align: center;
            font-size: 10pt;
        }
        .table-data td {
            padding: 5px 8px;
            font-size: 10pt;
        }
        .info-table td {
            border: none;
            padding: 2px 0;
            vertical-align: top;
            font-size: 11pt;
        }
        .info-label {
            width: 28%;
            font-weight: bold;
        }
        .info-colon {
            width: 3%;
        }
        .info-value {
            width: 69%;
        }
        .signature-block {
            margin-top: 30px;
            width: 100%;
        }
        .signature-left {
            width: 50%;
            float: left;
            text-align: left;
        }
        .signature-right {
            width: 50%;
            float: right;
            text-align: right;
        }
        .signature-center-right {
            margin-left: 50%;
            text-align: left;
            padding-left: 30px;
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
        .keterangan {
            font-size: 9pt;
            font-style: italic;
            color: #1f497d;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="header-logo">
                    @if(isset($logoData) && $logoData)
                        <img src="{{ $logoData }}" alt="Logo">
                    @endif
                </td>
                <td style="vertical-align: bottom; padding-bottom: 3px;">
                    {{-- empty right side, title goes below --}}
                </td>
            </tr>
        </table>
    </div>
    
    @yield('content')
    
</body>
</html>
