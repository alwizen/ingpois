<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoicePdfController extends Controller
{
    public function generate(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoice.pdf', ['invoice' => $invoice])
            ->setPaper('a4') // portrait default
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        return $pdf->stream('Invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function generateV2(Invoice $invoice) // NEW
    {
        // Contoh: layout beda + margin/ukuran beda (mis. landscape)
        $pdf = Pdf::loadView('invoice_v2.pdf', ['invoice' => $invoice])
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 8)
            ->setOption('margin-bottom', 8)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $pdf->stream('InvoiceV2-' . $invoice->invoice_number . '.pdf');
    }
}
