<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|Response
    {
        // Jika email sudah terverifikasi
        if ($request->user()->hasVerifiedEmail()) {
            // Periksa role user
            $role = $request->user()->type; // Sesuaikan dengan kolom role pada model User Anda

            // Arahkan ke dashboard sesuai dengan role
            switch ($role) {
                case 'personal':
                    return redirect()->route('home');

                default:
                    return redirect()->route('company.dashboard');
            }
        }

        // Jika email belum terverifikasi, tampilkan halaman verifikasi
        return Inertia::render('Auth/VerifyEmail', ['status' => session('status')]);
    }
}
