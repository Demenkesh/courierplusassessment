<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mails extends Mailable
{
    use Queueable, SerializesModels;

    public $mail;
    public $images = [];
    public $replymail;
    public $pdf;

    /**
     * Create a new message instance.
     */
    public function __construct($mail)
    {
        $this->mail = $mail;
        $this->images = $mail['images'] ?? [];
        $this->subject($mail['subject']);
        $this->replymail = $mail['email'] ?? '';
        $this->pdf = $mail['pdf'] ?? '';
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $email = $this->view('emails.mails')
            ->subject($this->mail['subject']);

        // Set the reply-to address if available
        if (!empty($this->replymail)) {
            $email->replyTo($this->replymail);
        }

        // Attach images if provided
        if (!empty($this->images)) {
            foreach ($this->images as $imagePath) {
                $email->attach($imagePath);
            }
        }

        // Attach PDF if provided
        if (!empty($this->pdf)) {
            $email->attach($this->pdf, [
                'as' => 'booking_details.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
