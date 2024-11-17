<?php

namespace Modules\ContactMessage\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Modules\ContactMessage\Http\Requests\ContactMessageValidate;
use Modules\ContactMessage\Mail\ContactMessageMail;
use Modules\ContactMessage\Models\ContactMessage;
use Modules\Support\Http\Controllers\SiteController;

class SiteContactMessageController extends SiteController
{
    public function create()
    {
        return view('contactMessage::contact-message-create');
    }

    public function store(ContactMessageValidate $request): RedirectResponse
    {
        $validatedData = $request->validated();

        ContactMessage::create($validatedData);

        // Mail::to('mail4mjaman@gmail.com')->send(new ContactMessageMail($validatedData));

        return back()->with('success', 'Your message has been sent.');
    }
}
