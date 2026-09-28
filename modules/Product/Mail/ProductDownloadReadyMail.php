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
    ) {
        $this->locale = ($permissions[0] ?? null)?->order?->locale;
    }

    public function envelope(): Envelope
    {
        $siteName = setting('branding.site_name', config('app.name'));

        return new Envelope(
            subject: __('product::mail.download_ready.subject', ['site' => $siteName]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'product::emails.download-ready',
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
