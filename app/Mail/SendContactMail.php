<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendContactMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $address = env('MAIL_FROM_ADDRESS', 'hello@wagenabled.com');
        $subject = 'Wag enabled contact mail.';
        $name = env('MAIL_FROM_NAME', 'Wag Enabled');

        return $this->view('emails.api.v1.contact.sendContactEmail')
                   ->from($address, $name)                                   
                   ->subject($subject)
                   ->replyTo($this->contact->email, $this->contact->name )
                   ->with(['contact' => $this->contact]);        
    }
}
