<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Menampilkan formulir reset password.
     *
     * **Apa:** Menampilkan formulir untuk mereset password.
     * **Mengapa:** Memungkinkan pengguna untuk mereset password mereka menggunakan token.
     * **Kapan:** Diakses setelah permintaan reset password.
     * **Di mana:** View Inertia `Auth/ResetPassword`.
     * **Siapa:** Pengguna yang telah meminta reset password.
     * **Bagaimana:** Mengambil email dan token pengguna dari permintaan dan meneruskannya ke view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
        ]);
    }

    /**
     * Menangani permintaan reset password baru.
     *
     * **Apa:** Memproses permintaan reset password.
     * **Mengapa:** Mereset password pengguna jika token valid dan password baru memenuhi syarat.
     * **Kapan:** Ketika pengguna mengirimkan formulir reset password.
     * **Di mana:** Metode controller.
     * **Siapa:** Pengguna yang telah meminta reset password.
     * **Bagaimana:**
     * 1. **Memvalidasi permintaan:** Memastikan token, email, dan password valid.
     * 2. **Mereset password:** Mencoba mereset password menggunakan metode `Password::reset`.
     * 3. **Memperbarui pengguna:** Jika berhasil, memperbarui password pengguna dan mengirim event `PasswordReset`.
     * 4. **Mengalihkan atau melempar pengecualian:** Mengalihkan ke halaman login jika berhasil, atau melempar pengecualian validasi dengan pesan kesalahan.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password telah berhasil direset');
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
