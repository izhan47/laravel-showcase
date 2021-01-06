<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UerPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $user, $token;

    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $address = env('MAIL_FROM_ADDRESS', 'hello@wagenabled.com');
        $subject = 'Wag enabled password reset mail.';
        $name = env('MAIL_FROM_NAME', 'Wag Enabled');

        $url = config("wagenabled.react_server_base_url") . "/reset-password/" . $this->token;

        return $this->view('emails.api.v1.auth.passwordReset')
                   ->from($address, $name)                                   
                   ->subject($subject)
                   ->with([ 'user' => $this->user, 'url' => $url]);        
    }
}
