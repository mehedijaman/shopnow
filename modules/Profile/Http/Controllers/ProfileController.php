<?php

namespace Modules\Profile\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Response;
use Modules\Profile\Http\Requests\UpdateEmailValidate;
use Modules\Profile\Http\Requests\UpdatePasswordValidate;
use Modules\Profile\Http\Requests\UpdateProfileValidate;
use Modules\Support\Http\Controllers\BackendController;

class ProfileController extends BackendController
{
    public function show(Request $request): Response
    {
        $user = $request->user();

        return inertia('Profile/ProfilePage', [
            'profileUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    public function update(UpdateProfileValidate $request): RedirectResponse
    {
        $user = $request->user();

        $user->update(['name' => $request->validated('name')]);

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated.');
    }

    public function updatePassword(UpdatePasswordValidate $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password updated.');
    }

    public function updateEmail(UpdateEmailValidate $request): RedirectResponse
    {
        $request->user()->update([
            'email' => $request->validated('email'),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Email address updated.');
    }
}
