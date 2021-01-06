<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCommentNotificaitonMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $watchAndLearn;

    public function __construct($watchAndLearn)
    {
        $this->watchAndLearn = $watchAndLearn;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $address = env('MAIL_FROM_ADDRESS', 'hello@wagenabled.com');
        $subject = 'Wag enabled watch and learn comment notification mail.';
        $name = env('MAIL_FROM_NAME', 'Wag Enabled');

        $url = config("wagenabled.react_server_base_url") . "/watch-and-learn/" . $this->watchAndLearn->slug;

        return $this->view('emails.api.v1.watchAndLearnComment.sendWatchAndLearnCommentNotificationEmail')
                   ->from($address, $name)                                   
                   ->subject($subject)
                   ->with(['watchAndLearn' => $this->watchAndLearn, 'url' => $url]);        
    }
}
