<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\ContactMessage\Models\ContactMessage;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Role::create(['name' => 'root']);
    $this->user->assignRole('root');

    $this->loggedRequest = $this->actingAs($this->user);

    $this->message = ContactMessage::create([
        'name' => 'John',
        'email' => 'john@test.com',
        'subject' => 'Test',
        'message' => 'Test message',
    ]);
});

test('contact message list can be rendered', function () {
    $response = $this->loggedRequest->get('/admin/contact-message');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ContactMessage/ContactMessageIndex')
            ->has(
                'messages.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->message->id)
                    ->where('name', $this->message->name)
                    ->where('email', $this->message->email)
                    ->where('subject', $this->message->subject)
                    ->where('message', $this->message->message)
                    ->etc()
            )
    );
});

test('contact message can be updated', function () {
    $response = $this->loggedRequest->put('/admin/contact-message/'.$this->message->id, [
        'name' => 'Updated Name',
        'email' => 'updated@test.com',
        'subject' => 'Updated Subject',
        'message' => 'Updated message',
    ]);

    $response->assertRedirect('/admin/contact-message');

    $redirectResponse = $this->loggedRequest->get('/admin/contact-message');
    $redirectResponse->assertInertia(
        fn (Assert $page) => $page
            ->component('ContactMessage/ContactMessageIndex')
            ->has(
                'messages.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->message->id)
                    ->where('name', 'Updated Name')
                    ->etc()
            )
    );
});

test('contact message can be deleted', function () {
    $response = $this->loggedRequest->delete('/admin/contact-message/'.$this->message->id);

    $response->assertRedirect('/admin/contact-message');

    $this->assertCount(0, ContactMessage::all());
});

test('contact message recycle bin can be rendered', function () {
    $this->loggedRequest->delete('/admin/contact-message/'.$this->message->id);

    $response = $this->loggedRequest->get('/admin/contact-message/recycle-bin');

    $response->assertStatus(200);

    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('ContactMessage/ContactMessageRecycleBin')
            ->has(
                'messages.data',
                1,
                fn (Assert $page) => $page
                    ->where('id', $this->message->id)
                    ->where('name', $this->message->name)
                    ->etc()
            )
    );
});

test('contact message can be restored', function () {
    $this->loggedRequest->delete('/admin/contact-message/'.$this->message->id);

    $response = $this->loggedRequest->get('/admin/contact-message/recycle-bin/'.$this->message->id.'/restore');

    $response->assertRedirect('/admin/contact-message/recycle-bin');

    $this->assertCount(1, ContactMessage::all());
});
