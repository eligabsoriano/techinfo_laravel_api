<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPass extends Mailable
{
    use Queueable, SerializesModels;

    public $resetPasswordOtp;

    /**
     * Create a new message instance.
     *
     * @param string $otp
     * @return void
     */
    public function __construct($resetPasswordOtp)
    {
        $this->resetPasswordOtp = $resetPasswordOtp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address', 'default@example.com'), config('mail.from.name', 'Default Name'))
            ->subject('Password Reset OTP')
            ->view('forgot-password ')
            ->with([
                'otp' => $this->resetPasswordOtp,
            ]);
    }
}
