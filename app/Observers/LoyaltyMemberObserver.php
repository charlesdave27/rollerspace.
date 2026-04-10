<?php

namespace App\Observers;

use App\Models\LoyaltyMember;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class LoyaltyMemberObserver
{
    /**
     * Handle the LoyaltyMember "created" event.
     */
    public function created(LoyaltyMember $loyaltyMember): void
    {
        $fileName = 'qrcodes/loyalty-member-' . $loyaltyMember->id . '.png';

        $directory = storage_path('app/public/qrcodes');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $result = (new Builder())->build(
            writer: new PngWriter(),
            data: url("/loyalty-member/{$loyaltyMember->id}"),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
        );

        $result->saveToFile(storage_path('app/public/' . $fileName));
        // Email the PNG
        Mail::to($loyaltyMember->email)->send(
            new \App\Mail\SendUserQrCode($loyaltyMember, $fileName)
        );
    }
    /**
     * Handle the LoyaltyMember "updated" event.
     */
    public function updated(LoyaltyMember $loyaltyMember): void
    {
        //
    }

    /**
     * Handle the LoyaltyMember "deleted" event.
     */
    public function deleted(LoyaltyMember $loyaltyMember): void
    {
        //
    }

    /**
     * Handle the LoyaltyMember "restored" event.
     */
    public function restored(LoyaltyMember $loyaltyMember): void
    {
        //
    }

    /**
     * Handle the LoyaltyMember "force deleted" event.
     */
    public function forceDeleted(LoyaltyMember $loyaltyMember): void
    {
        //
    }
}
