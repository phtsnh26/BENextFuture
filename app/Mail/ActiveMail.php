<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActiveMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data     =   $data;
    }

    public function build()
    {
        return $this->subject('Kích hoạt tài khoản NextFuture')
            ->view('Mail.viewActiveMail', [
                'data'      => $this->data
            ]);
    }
}
