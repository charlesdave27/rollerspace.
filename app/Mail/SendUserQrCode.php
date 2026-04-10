<?php

namespace App\Mail;

use App\Models\LoyaltyMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class SendUserQrCode extends Mailable
{
    use Queueable, SerializesModels;

    public $loyaltyMember;
    public $qrCodePath;

    /**
     * Create a new message instance.
     */
    public function __construct(LoyaltyMember $loyaltyMember, $qrCodePath)
    {
        $this->loyaltyMember = $loyaltyMember;
        $this->qrCodePath = $qrCodePath;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $qrCodeFullPath = storage_path('app/public/' . $this->qrCodePath);

        return $this->view('emails.loyalty_member_qrcode')
            ->subject('Your Loyalty Membership QR Code')
            ->withSymfonyMessage(function ($message) {
                $message->getHeaders()->addTextHeader('X-Entity-Ref-ID', (string) Str::uuid());
            })
            ->attach($qrCodeFullPath, [
                'as' => 'membership-qr.png',
                'mime' => 'image/png',
            ]);
    }
}
