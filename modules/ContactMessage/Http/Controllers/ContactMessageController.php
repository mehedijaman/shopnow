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
        $messages = ContactMessage::orderBy('name')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($message) => [
                'id' => $message->id,
                'name' => $message->name,
                'phone' => $message->phone,
                'email' => $message->email,
                'subject' => $message->subject,
                'message' => $message->message,

                // 'read_at' => $message->read_at->format('d/m/Y H:i') . 'h',
                // 'created_at' => $message->created_at->format('d/m/Y H:i') . 'h',
            ]);

        return inertia('ContactMessage/ContactMessageIndex', [
            'messages' => $messages,
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

    public function recycleBin(): Response
    {
        $messages = ContactMessage::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($message) => [
                'id' => $message->id,
                'name' => $message->name,
                'phone' => $message->phone,
                'email' => $message->email,
                'subject' => $message->subject,
                'message' => $message->message,
            ]);

        return inertia('ContactMessage/ContactMessageRecycleBin', [
            'messages' => $messages,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        ContactMessage::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('contactMessage.recycleBin.index')
            ->with('success', 'Message restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $message = ContactMessage::onlyTrashed()->findOrFail($id);

        $message->forceDelete();

        return redirect()->route('contactMessage.recycleBin.index')->with('success', 'Message deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $messages = ContactMessage::onlyTrashed()->get();

        foreach ($messages as $message) {
            $message->forceDelete();
        }

        return redirect()->route('contactMessage.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        ContactMessage::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('contactMessage.recycleBin.index')
            ->with('success', 'Message restored.');
    }
}
