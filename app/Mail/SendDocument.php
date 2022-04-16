<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use App\Support\PDF;
use TeamManager;

class SendDocument extends Mailable
{
    use Queueable, SerializesModels;

    protected $model;

    public $info;

    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Model $model, $info, $type, $team = null)
    {
        $this->model = $model;
        $this->info = $info;
        $this->type = $type;

        if($team) {
            TeamManager::setTeam($team);
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(PDF $pdf)
    {
        $string = $pdf
            ->string('app.pdf.'.$this->type, [
                'model' => $this->model
            ]);

        $filename = $this->type.'_'.$this->model->number;

        $bcc = [];

        if($this->info['bcc']) {
            array_push($bcc, $this->info['bcc']);
        }

        return $this
            ->subject($this->info['subject'])
            ->to($this->info['email_to'])
            ->bcc($bcc)
            ->html($this->info['message'])
            ->attachData($string, $filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
