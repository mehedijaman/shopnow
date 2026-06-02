<?php

namespace Modules\ContactMessage\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\ContactMessage\Http\Requests\ContactMessageValidate;
use Modules\ContactMessage\Models\ContactMessage;
use Modules\Settings\Services\MetaConversionApiService;
use Modules\Support\Http\Controllers\SiteController;

class SiteContactMessageController extends SiteController
{
    public function create()
    {
        return view('contactMessage::contact-message-create');
    }

    public function store(ContactMessageValidate $request, MetaConversionApiService $metaConversionApiService): RedirectResponse
    {
        $validatedData = $request->validated();

        $contactMessage = ContactMessage::create($validatedData);

        $metaConversionApiService->track('Lead', [
            'content_name' => 'Contact Form Submission',
            'content_category' => 'contact',
        ], [
            'event_id' => 'lead_'.$contactMessage->id,
            'event_source_url' => url('/contact'),
            'consent_granted' => $request->cookie('tracking_consent') === 'granted',
            'client_ip_address' => $request->ip(),
            'client_user_agent' => $request->userAgent(),
            'fbp' => $request->cookie('_fbp'),
            'fbc' => $request->cookie('_fbc'),
            'email' => $validatedData['email'] ?? null,
            'phone' => $validatedData['phone'] ?? null,
            'first_name' => $validatedData['name'] ?? null,
            'external_id' => (string) $contactMessage->id,
        ]);

        // Mail::to('mail4mjaman@gmail.com')->send(new ContactMessageMail($validatedData));

        return back()->with('success', 'Your message has been sent.');
    }
}
