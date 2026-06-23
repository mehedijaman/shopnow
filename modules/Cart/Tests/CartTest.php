<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('site cart page loads', function () {
    $response = $this->get('/cart');

    $response->assertStatus(200);
});

test('checkout page loads', function () {
    $response = $this->get('/checkout');

    $response->assertStatus(200);
});
