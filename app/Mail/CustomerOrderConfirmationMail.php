<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Models\Order;

class CustomerOrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order)
    {
        $this->locale = $order->locale;
    }

    public function envelope(): Envelope
    {
        $siteName = setting('branding.site_name', config('app.name'));

        return new Envelope(
            subject: __('order::mail.confirmation.subject', [
                'id' => $this->order->id,
                'site' => $siteName,
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'order::emails.customer-order-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
