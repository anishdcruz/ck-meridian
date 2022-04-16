<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvitationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invitation, $user)
    {
        $this->invitation = $invitation;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->user
            ? 'app.invitations.existing'
            : 'app.invitations.new';

        return $this
            ->subject('New Invitation!')
            ->to($this->invitation->email)
            ->markdown($email)
            ->with(['invitation' => $this->invitation]);
    }
}
