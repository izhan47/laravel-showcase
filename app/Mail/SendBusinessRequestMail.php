<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBusinessRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $businessRequest;

    public function __construct($businessRequest)
    {
        $this->businessRequest = $businessRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $address = env('MAIL_FROM_ADDRESS', 'hello@wagenabled.com');
        $subject = 'Wag enabled business request mail.';
        $name = env('MAIL_FROM_NAME', 'Wag Enabled');

        return $this->view('emails.api.v1.businessRequest.sendBusinessRequestEmail')
                   ->from($address, $name)                                   
                   ->subject($subject)
                   ->replyTo($this->businessRequest->contact_email, $this->businessRequest->first_name .' '. $this->businessRequest->last_name )
                   ->with(['businessRequest' => $this->businessRequest]);        
    }
}
