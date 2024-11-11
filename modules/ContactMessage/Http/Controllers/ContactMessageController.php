<?php

namespace Modules\ContactMessage\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\ContactMessage\Http\Requests\ContactMessageValidate;
use Modules\ContactMessage\Models\ContactMessage;
use Modules\Support\Http\Controllers\BackendController;

class ContactMessageController extends BackendController
{
    public function index(): Response
    {
        $contactMessages = ContactMessage::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($contactMessage) => [
                'id' => $contactMessage->id,
                'name' => $contactMessage->name,
                'created_at' => $contactMessage->created_at->format('d/m/Y H:i').'h',
            ]);

        return inertia('ContactMessage/ContactMessageIndex', [
            'contactMessages' => $contactMessages,
        ]);
    }

    public function create(): Response
    {
        return inertia('ContactMessage/ContactMessageForm');
    }

    public function store(ContactMessageValidate $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return redirect()->route('contactMessage.index')
            ->with('success', 'ContactMessage created.');
    }

    public function edit(int $id): Response
    {
        $contactMessage = ContactMessage::find($id);

        return inertia('ContactMessage/ContactMessageForm', [
            'contactMessage' => $contactMessage,
        ]);
    }

    public function update(ContactMessageValidate $request, int $id): RedirectResponse
    {
        $contactMessage = ContactMessage::findOrFail($id);

        $contactMessage->update($request->validated());

        return redirect()->route('contactMessage.index')
            ->with('success', 'ContactMessage updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        ContactMessage::findOrFail($id)->delete();

        return redirect()->route('contactMessage.index')
            ->with('success', 'ContactMessage deleted.');
    }
}
