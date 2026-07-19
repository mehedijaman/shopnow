<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test("the site's index page returns a successful response", function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});
