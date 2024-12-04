<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Menampilkan halaman formulir permintaan reset password.
     *
     * **Apa:** Menampilkan halaman web yang berisi formulir untuk memasukkan alamat email.
     * **Siapa:** Pengguna yang lupa password.
     * **Kapan:** Ketika pengguna mengklik link "Lupa Password" atau link serupa.
     * **Di mana:** Halaman web dengan URL yang ditentukan dalam routing.
     * **Mengapa:** Memberikan kesempatan kepada pengguna untuk mereset password mereka.
     * **Bagaimana:** Menggunakan Inertia.js untuk merender komponen 'Auth/ForgotPassword' dan mengirimkan data status (jika ada) ke komponen tersebut.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Menerima dan memproses permintaan reset password.
     *
     * **Apa:** Menerima data dari formulir reset password (alamat email) dan memprosesnya.
     * **Siapa:** Pengguna yang telah mengisi formulir.
     * **Kapan:** Ketika pengguna mengirimkan formulir.
     * **Di mana:** Rute yang menangani permintaan POST untuk formulir reset password.
     * **Mengapa:** Mengirimkan link reset password ke alamat email yang diberikan.
     * **Bagaimana:**
     *   1. Validasi input: Memastikan alamat email valid.
     *   2. Mengirim link reset password: Menggunakan fungsi `Password::sendResetLink` untuk mengirim email dengan link reset password.
     *   3. Menampilkan pesan: Memberikan feedback kepada pengguna berdasarkan hasil pengiriman link.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Mengirim link reset password
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Menampilkan pesan yang sesuai
        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Email telah dikirim. Silakan cek email Anda.');
        }

        // Menampilkan pesan error jika terjadi kesalahan
        throw ValidationException::withMessages([
            'email' => ['Email yang dimasukkan tidak benar.'],
        ]);
    }
}
