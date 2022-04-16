<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;

class SendNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $info;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($info)
    {
        $this->info = $info;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $bcc = [];

        if($this->info['bcc']) {
            array_push($bcc, $this->info['bcc']);
        }

        return $this
            ->subject($this->info['subject'])
            ->to($this->info['email_to'])
            ->bcc($bcc)
            ->html($this->info['message']);
    }
}
