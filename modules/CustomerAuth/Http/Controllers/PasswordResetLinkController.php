<?php

namespace Modules\CustomerAuth\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Support\Http\Controllers\AppController;

class PasswordResetLinkController extends AppController
{
    /**
     * Display the password reset link request view.
     *
     * @return View
     */
    public function forgotPasswordForm()
    {
        return view('customer-auth::forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @return RedirectResponse
     *
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::broker('customersModule')->sendResetLink(
            $request->only('email')
        );

        return $status == Password::broker('customersModule')::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
