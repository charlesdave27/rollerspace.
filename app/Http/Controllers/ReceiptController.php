<?php

namespace App\Http\Controllers;

use App\Models\RentalManagement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReceiptController extends Controller
{
    public function generatePdf($id)
    {
        $rental = RentalManagement::with(['loyaltyMember', 'rentalPackage', 'reward', 'equipment', 'equipments'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('receipts.rental-receipt', [
            'rental' => $rental
        ]);

        $filename = 'rental-receipt-' . str_pad($rental->id, 6, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    public function viewPdf($id)
    {
        $rental = RentalManagement::with(['loyaltyMember', 'rentalPackage', 'reward', 'equipment', 'equipments'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('receipts.rental-receipt', [
            'rental' => $rental
        ]);

        return $pdf->stream('rental-receipt-' . str_pad($rental->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}


