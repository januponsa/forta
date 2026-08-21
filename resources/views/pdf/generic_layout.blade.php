<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $template->name ?? 'Document' }}</title>
    <style>
        @page {
            size: {{ $template->paper_size ?? 'A4' }};
            margin-top: {{ $template->margin_top ?? 30 }}px;
            margin-bottom: {{ $template->margin_bottom ?? 30 }}px;
            margin-left: {{ $template->margin_left ?? 30 }}px;
            margin-right: {{ $template->margin_right ?? 30 }}px;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Layout classes */
        .header {
            margin-bottom: 20px;
        }
        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 50px;
        }
        .page-break {
            page-break-after: always;
        }
        
        /* Utility for signature area */
        .signature-table {
            border: none;
            width: 100%;
        }
        .signature-table td {
            border: none;
            padding: 0;
            text-align: left;
        }
    </style>
</head>
<body>

    @if(!empty($header_html))
        <div class="header">
            {!! $header_html !!}
        </div>
    @endif

    <div class="content">
        {!! $body_html !!}
    </div>

    @if(!empty($footer_html))
        <div class="footer">
            {!! $footer_html !!}
        </div>
    @endif

</body>
</html>
