<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('contact page loads', function () {
    $response = $this->get('/contact');

    $response->assertStatus(200);
});

test('contact message can be submitted', function () {
    Http::fake();

    $response = $this->post('/contact', [
        'name' => 'John',
        'email' => 'john@test.com',
        'phone' => '1234567890',
        'subject' => 'Test Subject',
        'message' => 'Test message body',
    ]);

    $response->assertRedirect();
});
