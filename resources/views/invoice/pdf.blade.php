<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        /* === Watermark Status === */
        .wm-status {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 90px;
            font-weight: 900;
            text-transform: uppercase;
            z-index: 0;
            white-space: nowrap;
            pointer-events: none;
        }

        /* Pastikan konten tetap di atas watermark */
        body>*:not(.wm-status) {
            position: relative;
            z-index: 2;
        }


        @media print {
            .wm-status {
                opacity: 0.08;
            }

            /* sedikit lebih tegas saat print */
        }

        /* Pastikan konten utama di atas watermark */
        .container,
        .top-section,
        table,
        .footer-note {
            position: relative;
            z-index: 1;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }

        .summary-table th {
            text-align: left;
            border-bottom: 1px solid #ccc;
            padding: 6px 4px;
        }

        .summary-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #eee;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
            font-size: 13px;
        }


        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Header */
        .top-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 50px;
        }

        .top-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            margin-top: 5px;
        }

        .company-header {
            font-size: 18px;
            font-weight: bold;
        }

        .info-box {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .info-label {
            font-weight: bold;
            margin-right: 10px;
        }

        /* Title */
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }

        /* Summary */
        .summary-section {
            margin-bottom: 20px;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .summary-box {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #aaa;
            padding-top: 6px;
            margin-top: 6px;
        }

        .numeric {
            text-align: right;
            white-space: nowrap;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: left;
        }

        table .numeric {
            text-align: right;
        }

        /* Footer */
        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #555;
            padding: 10px 0;
            border-top: 1px solid #ccc;
            background: #fff;
            /* supaya jelas kalau ada konten panjang */
        }

        .tale-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: none;
        }

        .table-info td {
            border: none;
            vertical-align: middle;
        }


        .letterhead {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: none;
        }

        .letterhead td {
            border: none;
            vertical-align: middle;
        }

        .lh-left {
            width: 60%;
            padding: 6px 0;
        }

        .lh-right {
            width: 40%;
            text-align: right;
            padding: 6px 0;
            font-size: 12px;
            line-height: 1.3;
        }

        .lh-logo {
            display: inline-block;
            height: 34px;
            /* sesuaikan tinggi logo */
            max-height: 38px;
        }

        .lh-invoice-no {
            font-weight: 700;
            color: #5f6060;
        }

        .lh-billing {
            color: #5a5a5a;
            font-size: 12px;
        }

        .lh-billing a {
            color: #1a73e8;
            /* biru seperti contoh */
            text-decoration: none;
        }

        .lh-divider {
            border: 0;
            border-top: 1px solid #e0e0e0;
            margin: 8px 0 18px;
        }

        .table-info td {
            vertical-align: top;
            padding: 5px;
        }

        /* Supaya alamat panjang turun ke bawah */
        .wrap-text {
            white-space: normal;
            word-wrap: break-word;
            max-width: 250px;
            /* batas lebar kolom, bisa disesuaikan */
        }
    </style>


    {{-- LETTERHEAD --}}
    <table class="letterhead">
        <tr>
            <td class="lh-left">
                @php
                    // Pakai accessor yang aman untuk PDF
                    $logoPath = $invoice->company->logo_path_for_pdf;
                @endphp

                @if ($logoPath)
                    <img src="{{ $logoPath }}" class="lh-logo" alt="{{ $invoice->company->name }}">
                @elseif($invoice->company->logo_url)
                    {{-- fallback untuk preview HTML (kalau bukan PDF) --}}
                    <img src="{{ $invoice->company->logo_url }}" class="lh-logo" alt="{{ $invoice->company->name }}">
                @else
                    <strong>{{ $invoice->company->name }}</strong>
                @endif
            </td>
            <td class="lh-right">
                <div class="lh-invoice-no">{{ $invoice->invoice_number }}</div>
            </td>
        </tr>
    </table>

    <hr class="lh-divider">

</head>

<body>
    <div class="container">

        @php
            $isPaid = $invoice->status === 'paid';
            $labelWatermark = $isPaid ? 'PAID' : 'UNPAID';
            $color = $isPaid
                ? 'rgba(84, 214, 34, 0.52)' // hijau lembut
                : 'rgba(81, 90, 78, 0.52)'; // abu lembut
        @endphp

        <div class="wm-status" style="color: {{ $color }}">{{ $labelWatermark }}</div>


        <!-- Top Section: Company Info & Invoice Meta -->
        <div class="top-section">
            <div class="top-info">
                <div class="info-box">
                    <div class="info-label">Invoice Number: #{{ $invoice->invoice_number }}</div>
                </div><br>

                <table class="table-info">
                    <tr>
                        <td class="wrap-text">Invoiced To : {{ $invoice->recipient }}</td>
                        <td>Invoice Date: {{ $invoice->invoice_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="wrap-text">{{ $invoice->recipient_address }}</td>
                        @if ($invoice->due_date)
                            <td>Due Date: {{ $invoice->due_date->format('d/m/Y') }}</td>
                        @endif
                    </tr>
                </table>

            </div>


            <!-- Items Table -->
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Description</th>
                        <th style="width: 15%;" class="numeric">Qty</th>
                        <th style="width: 20%;" class="numeric">Price (Rp)</th>
                        <th style="width: 15%;" class="numeric">Amount (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $item->title }}</td>
                            <td class="numeric">{{ $item->quantity }}</td>
                            <td class="numeric">{{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="numeric">{{ number_format($item->nominal * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    <!-- Summary -->
                    <tr>
                        <td colspan="3" style="text-align: right;">Subtotal</td>
                        <td class="numeric">{{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>

                    @if ($invoice->use_ppn)
                        {{-- <tr>
                            <td colspan="3" style="text-align: right;">Total excl. VAT</td>
                            <td class="numeric">{{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                        </tr> --}}
                        <tr>
                            <td colspan="3" style="text-align: right;">VAT ({{ $invoice->ppn_percentage }}%)</td>
                            <td class="numeric">{{ number_format($invoice->ppn_amount, 0, ',', '.') }}</td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Total</td>
                        <td class="numeric" style="font-weight: bold;">
                            {{ number_format($invoice->total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>


            <!-- Footer Note -->
            <div class="footer-note">
                <div>{{ $invoice->company->name }}</div>
                <div>{{ $invoice->company->address }}</div>
                <p>Invoice has been generated on {{ now()->format('d F Y') }}</p>
            </div>

        </div>
</body>

</html>
