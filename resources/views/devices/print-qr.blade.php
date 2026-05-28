<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }} | WO Medika</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #0f172a;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        .toolbar {
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            margin: 0 0 6mm;
            padding: 0 0 4mm;
        }

        .toolbar h1 {
            font-size: 18px;
            margin: 0;
        }

        .toolbar p {
            color: #475569;
            font-size: 11px;
            margin: 1mm 0 0;
        }

        .actions {
            display: flex;
            gap: 2mm;
        }

        .actions a,
        .actions button {
            background: #0e7490;
            border: 0;
            border-radius: 4px;
            color: #ffffff;
            cursor: pointer;
            font-size: 11px;
            font-weight: 700;
            padding: 2.5mm 4mm;
            text-decoration: none;
        }

        .actions a {
            background: #334155;
        }

        .sheet {
            border-collapse: collapse;
            margin: 0;
            table-layout: fixed;
            width: 190mm;
        }

        .sheet td {
            height: 82mm;
            padding: 1.5mm;
            vertical-align: top;
            width: 33.333%;
        }

        .label {
            border: 1px dashed #000000;
            height: 76mm;
            margin: 0 auto;
            overflow: hidden;
            padding: 3mm;
            page-break-inside: avoid;
            text-align: center;
            width: 58mm;
        }

        .brand {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            color: #0e7490;
            text-transform: uppercase;
        }

        .qr {
            margin: 1.5mm auto 1.5mm;
            text-align: center;
        }

        .qr img {
            height: 40mm;
            width: 40mm;
        }

        .qr svg {
            height: 40mm;
            width: 40mm;
        }

        .code {
            margin: 0 0 1.5mm;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.15;
            word-break: break-word;
        }

        .name {
            margin: 0;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.2;
            word-break: break-word;
        }

        .meta {
            margin: 1.5mm 0 0;
            color: #475569;
            font-size: 10px;
            line-height: 1.25;
            word-break: break-word;
        }

        @media print {
            .toolbar {
                display: none;
            }
        }
    </style>
</head>
<body>
    @if (($renderMode ?? 'html') !== 'pdf')
        <header class="toolbar">
            <div>
                <h1>{{ $title }}</h1>
                <p>{{ $devices->count() }} label alat siap dicetak.</p>
            </div>
            <div class="actions">
                <button type="button" onclick="window.print()">Cetak</button>
                <a href="{{ route('devices.print-qr', ['format' => 'pdf']) }}">Unduh PDF</a>
            </div>
        </header>
    @endif

    <table class="sheet">
        @foreach ($devices->chunk(3) as $row)
            <tr>
                @foreach ($row as $device)
                    <td>
                        <section class="label">
                            <div class="brand">WO Medika</div>
                            <div class="qr">
                                @if (($renderMode ?? 'html') === 'pdf')
                                    <img src="{{ $device['qr_image'] }}" alt="QR {{ $device['barcode_code'] }}">
                                @else
                                    {!! $device['qr_svg'] !!}
                                @endif
                            </div>
                            <p class="code">{{ $device['barcode_code'] }}</p>
                            <p class="name">{{ $device['name'] }}</p>
                            <p class="meta">
                                {{ $device['inventory_number'] }}<br>
                                {{ $device['unit'] }}<br>
                                SN: {{ $device['serial_number'] }}
                            </p>
                        </section>
                    </td>
                @endforeach

                @for ($column = $row->count(); $column < 3; $column++)
                    <td></td>
                @endfor
            </tr>
        @endforeach
    </table>
</body>
</html>
