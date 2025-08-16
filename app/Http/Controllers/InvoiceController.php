<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generatePdf(Invoice $invoice)
    {
        // Load the invoice with all relationships
        $invoice->load(['customer', 'motorcycle', 'workOrderItems', 'payments', 'workOrder']);

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice'));

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Return the PDF for download
        return $pdf->download("Invoice-{$invoice->number}.pdf");
    }

    public function viewPdf(Invoice $invoice)
    {
        // Load the invoice with all relationships
        $invoice->load(['customer', 'motorcycle', 'workOrderItems', 'payments', 'workOrder']);

        // Generate PDF
        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice'));

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Return the PDF for viewing in browser
        return $pdf->stream("Invoice-{$invoice->number}.pdf");
    }
}
