<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->redirectBasedOnType($request->user());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->redirectBasedOnType($request->user());
    }

    /**
     * Redirect based on user type.
     */
    protected function redirectBasedOnType($user): RedirectResponse
    {
        if ($user->type === 'company') {
            return redirect()->route('company.dashboard', ['verified' => 1]);
        }

        return redirect()->route('home', ['verified' => 1]);
    }
}
