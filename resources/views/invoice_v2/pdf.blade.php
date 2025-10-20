<!-- resources/views/invoice_v2/pdf.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice (V2) #{{ $invoice->invoice_number }}</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .wrap {
            padding: 10mm;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 8mm;
            border-bottom: 2px solid #444;
            padding-bottom: 6mm;
        }

        .col {
            display: table-cell;
            vertical-align: middle;
        }

        .col.logo {
            width: 30%;
        }

        .col.info {
            width: 70%;
            text-align: right;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .muted {
            color: #666;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6mm;
        }

        .grid th,
        .grid td {
            border: 1px solid #999;
            padding: 6px;
        }

        .grid th {
            background: #f2f2f2;
            text-transform: uppercase;
            font-size: 10px;
        }

        .totals {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
            margin-top: 6mm;
        }

        .totals td {
            padding: 6px;
            border: 1px solid #999;
        }

        .totals tr:last-child td {
            font-weight: 700;
            background: #f8f8f8;
        }

        /* Ribbon status di pojok kanan atas (versi kamu kemarin) */
        .ribbon-status {
            position: fixed;
            top: -5px;
            right: -130px;
            width: 300px;
            text-align: center;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 6px 0;
            transform: rotate(45deg);
            box-shadow: 0 2px 6px rgba(0, 0, 0, .2);
            z-index: 1000;
            pointer-events: none;
            background:
                {{ $invoice->status === 'paid' ? '#22c55e' : '#9ca3af' }}
            ;
        }
    </style>
</head>

<body>
    <div class="wrap">

        <div class="ribbon-status">{{ $invoice->status }}</div>

        <div class="header">
            {{-- <div class="col logo">
                @if(optional($invoice->company)->logo)
                <img src="{{ $invoice->company->logo }}" alt="" style="max-height:60px;">
                @else
                <div style="font-weight:700;font-size:16px;">{{ $invoice->company->name ?? '' }}</div>
                @endif
            </div> --}}
            <div class="col info">
                <div class="title">INVOICE</div>
                <div>#{{ $invoice->invoice_number }}</div>
                <div class="muted">
                    Tanggal Invoice: {{ optional($invoice->invoice_date)->format('d/m/Y') ?? '-' }}
                    {{ optional($invoice->due_date)->format('d/m/Y') ?? '' }}
                </div>
                @if($invoice->status === 'paid' && $invoice->paid_at)
                    <div class="muted">Dibayar: {{ $invoice->paid_at->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>

        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; vertical-align:top; padding-right:10px;">
                    <strong>Ditujukan Kepada</strong><br>
                    {{ $invoice->recipient }}<br>
                    {!! nl2br(e($invoice->recipient_address)) !!}
                </td>
                {{-- <td style="width:50%; vertical-align:top; text-align:right; padding-left:10px;">
                    <strong>Perusahaan</strong><br>
                    {{ $invoice->company->name ?? '-' }}<br>
                    {!! nl2br(e($invoice->company->address ?? '-')) !!}<br>
                    {{ $invoice->company->phone ?? '' }}
                </td> --}}
            </tr>
        </table>

        <table class="grid">
            <thead>
                <tr>
                    <th style="width:6%;">No</th>
                    <th>Deskripsi</th>
                    <th style="width:12%;">Qty</th>
                    <th style="width:16%;">Harga</th>
                    <th style="width:16%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $i => $item)
                    <tr>
                        <td style="text-align:center;">{{ $i + 1 }}</td>
                        <td>{{ $item->title }}</td>
                        <td style="text-align:right;">{{ number_format($item->quantity, 0, ',', '.') }}
                            {{ $item->unit ?? '' }}
                        </td>
                        <td style="text-align:right;">{{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td style="text-align:right;">{{ number_format($item->quantity * $item->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $subtotal = $invoice->items->sum(fn($it) => $it->quantity * $it->nominal);
            $discount = $invoice->discount ?? 0;
            $tax = $invoice->tax ?? 0; // nominal pajak, atau ganti perhitungan 11% dsb sesuai kebutuhan
            $total = $subtotal - $discount + $tax;
        @endphp

        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td style="text-align:right;">{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            @if($discount > 0)
                <tr>
                    <td>Diskon</td>
                    <td style="text-align:right;">-{{ number_format($discount, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if($tax > 0)
                <tr>
                    <td>Pajak</td>
                    <td style="text-align:right;">{{ number_format($tax, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td>Total</td>
                <td style="text-align:right;">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p style="margin-top:15mm;" class="muted">
            Catatan: {{ $invoice->note }}.
        </p>
    </div>
</body>

</html>