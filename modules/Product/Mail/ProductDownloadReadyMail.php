<?php

namespace Modules\Product\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Product\Models\DownloadPermission;

class ProductDownloadReadyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, DownloadPermission>  $permissions
     */
    public function __construct(
        public readonly array $permissions,
    ) {}

    public function envelope(): Envelope
    {
        $siteName = setting('branding.site_name', config('app.name'));

        return new Envelope(
            subject: 'Your Downloads Are Ready — '.$siteName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'product::emails.download-ready',
            with: [
                'permissions' => $this->permissions,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
