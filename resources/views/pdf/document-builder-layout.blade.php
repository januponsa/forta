<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document</title>
    <style>
        @page {
            size: {{ $template->paper_size ?? 'A4' }} {{ $template->orientation ?? 'portrait' }};
            margin: 0; /* Let Browsershot handle margins, or handle here if preferred. Currently relying on Browsershot margins. */
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
            /* If Browsershot handles margin, we just set padding 0. */
        }
        .header {
            margin-bottom: 20px;
            width: 100%;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo {
            width: 100px;
            text-align: center;
        }
        .header-logo img {
            max-width: 100px;
            max-height: 100px;
        }
        .header-text {
            flex-grow: 1;
            text-align: center;
            padding: 0 10px;
        }
        .univ-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .faculty-name {
            font-size: 14pt;
            font-weight: bold;
        }
        .prodi-name {
            font-size: 12pt;
            font-weight: bold;
        }
        .address-text {
            font-size: 9pt;
            margin-top: 5px;
        }
        .separator {
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .content-body {
            text-align: justify;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        
        /* Utility classes for flow editor content */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .italic { font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 5px; }
    </style>
</head>
<body>

    @if($letterhead)
        <div class="header">
            @if($letterhead->header_html)
                <div class="header-html-content">
                    {!! $letterhead->header_html !!}
                </div>
            @else
                <div class="header-content">
                    <div class="header-logo">
                        @if($letterhead->logo_asset_id && $letterhead->logoAsset->activeVersion)
                            {{-- Read file as base64 for reliable PDF rendering without network requests --}}
                            @php
                                $path = storage_path('app/public/' . $letterhead->logoAsset->activeVersion->original_path);
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = file_exists($path) ? file_get_contents($path) : '';
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            @endphp
                            @if($data)
                                <img src="{{ $base64 }}" alt="Logo">
                            @endif
                        @endif
                    </div>
                    <div class="header-text">
                        <div class="univ-name">{{ $letterhead->master->university_name }}</div>
                        @if($letterhead->master->faculty)
                            <div class="faculty-name">{{ $letterhead->master->faculty }}</div>
                        @endif
                        @if($letterhead->master->study_program)
                            <div class="prodi-name">{{ $letterhead->master->study_program }}</div>
                        @endif
                        <div class="address-text">
                            {{ $letterhead->master->campus_address }}<br>
                            Telp: {{ $letterhead->master->phone ?? '-' }} | Email: {{ $letterhead->master->email ?? '-' }} | Web: {{ $letterhead->master->website ?? '-' }}
                        </div>
                    </div>
                    <div class="header-logo" style="visibility: hidden;">
                        <!-- Spacer to balance the flexbox -->
                        <img src="" style="width: 100px;">
                    </div>
                </div>
            @endif
            
            <div class="separator" style="
                border-bottom: {{ $letterhead->separator_width }}px {{ $letterhead->separator_style }} {{ $letterhead->separator_color }};
            "></div>
        </div>
    @endif

    <div class="content-body">
        {!! $bodyHtml !!}
    </div>

    @if($letterhead && $letterhead->footer_html)
        <div class="footer">
            {!! $letterhead->footer_html !!}
        </div>
    @endif

</body>
</html>
