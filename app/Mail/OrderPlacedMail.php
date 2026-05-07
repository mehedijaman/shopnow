<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Models\Order;

class OrderPlacedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function envelope(): Envelope
    {
        $siteName = setting('branding.site_name', config('app.name'));

        return new Envelope(
            subject: 'New Order #'.$this->order->id.' — '.$siteName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'order::emails.order-placed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
