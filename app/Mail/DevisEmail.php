<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class DevisEmail extends Mailable
{
    public string $corps;

    public function __construct(
        public string $destinataire,
        public string $objet,
        string $corps,
        public string $pdfContent,
        public string $pdfNom
    ) {
        $this->corps = $corps;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->objet);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.emailDevis');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfNom)
                ->withMime('application/pdf'),
        ];
    }
}
