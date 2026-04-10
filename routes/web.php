<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/api/loyalty-member/{id}', function ($id) {
    $member = \App\Models\LoyaltyMember::find($id);

    if ($member) {
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $member->id,
                'name' => $member->name,
                'loyalty_points' => $member->loyalty_points ?? 0,
            ]
        ]);
    }

    return response()->json(['success' => false], 404);
})->middleware(['web', 'auth']);

Route::get('/admin/receipt/{id}/pdf', [App\Http\Controllers\ReceiptController::class, 'generatePdf'])
    ->name('receipt.pdf')
    ->middleware(['web', 'auth']);
