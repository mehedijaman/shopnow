<?php

use Inertia\Testing\AssertableInertia as Assert;
use Modules\Settings\Models\SmsMessage;
use Modules\User\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->admin = User::factory()->create();
    Role::firstOrCreate(['name' => 'root', 'guard_name' => 'user']);
    $this->admin->assignRole('root');
});

test('sms history requires an authenticated user with history or test permissions', function () {
    $this->get('/admin/settings/sms/history')->assertRedirect();
    $user = User::factory()->create();
    $this->actingAs($user, 'user')->get('/admin/settings/sms/history')->assertForbidden();
    $user->givePermissionTo('sms-settings-edit');
    $this->get('/admin/settings/sms/history')->assertForbidden();
});

test('sms history is a separate paginated page with masked recipients and no message bodies', function () {
    $messages = SmsMessage::factory()->count(30)->create([
        'phone' => '01712345678', 'body' => 'Private OTP 123456', 'provider' => 'bulksmsbd',
    ]);
    $this->actingAs($this->admin, 'user')->get('/admin/settings/sms/history')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Settings/SmsHistory')
            ->has('messages.data', 25)
            ->where('messages.total', 30)
            ->where('messages.last_page', 2)
            ->where('messages.data.0.id', $messages->last()->id)
            ->where('messages.data.0.recipient', '017*****678')
            ->where('providers.bulksmsbd', 'BulkSMSBD')
            ->where('canEditSettings', true)
            ->missing('messages.data.0.phone')
            ->missing('messages.data.0.body')
            ->missing('settings'));
    $this->get('/admin/settings/sms/history?page=2')->assertInertia(fn (Assert $page) => $page
        ->has('messages.data', 5)
        ->where('messages.current_page', 2)
        ->where('messages.data.0.id', $messages[4]->id));
});

test('history permission allows the complete list independently of settings access', function () {
    $viewer = User::factory()->create();
    $viewer->givePermissionTo('sms-activity-view');
    SmsMessage::factory()->count(2)->create();
    $this->actingAs($viewer, 'user')->get('/admin/settings/sms/history')->assertInertia(fn (Assert $page) => $page
        ->component('Settings/SmsHistory')
        ->has('messages.data', 2)
        ->where('canEditSettings', false));
});

test('test senders see only their own test messages on the history page', function () {
    $tester = User::factory()->create();
    $tester->givePermissionTo('sms-test-send');
    $own = SmsMessage::factory()->create(['purpose' => 'test', 'created_by' => $tester->id]);
    SmsMessage::factory()->create(['purpose' => 'test', 'created_by' => $this->admin->id]);
    SmsMessage::factory()->create(['purpose' => 'otp', 'created_by' => $tester->id]);
    SmsMessage::factory()->create(['purpose' => 'order_placed']);
    $this->actingAs($tester, 'user')->get('/admin/settings/sms/history')->assertInertia(fn (Assert $page) => $page
        ->has('messages.data', 1)
        ->where('messages.total', 1)
        ->where('messages.data.0.id', $own->id));
});

test('sms history handles an empty list', function () {
    $this->actingAs($this->admin, 'user')->get('/admin/settings/sms/history')->assertInertia(fn (Assert $page) => $page
        ->component('Settings/SmsHistory')
        ->has('messages.data', 0)
        ->where('messages.total', 0));
});

test('sms settings expose history navigation access without loading history records', function () {
    SmsMessage::factory()->create();
    $this->actingAs($this->admin, 'user')->get('/admin/settings/sms')->assertInertia(fn (Assert $page) => $page
        ->component('Settings/SettingsForm')
        ->where('sms.can_view_activity', true)
        ->missing('messages'));
    expect(route('settings.sms.history'))->toEndWith('/admin/settings/sms/history');
});
