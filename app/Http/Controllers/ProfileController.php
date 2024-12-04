<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman form profil pengguna untuk diedit.
     *
     * @param \Illuminate\Http\Request $request Objek permintaan pengguna.
     * @return \Illuminate\Http\Response Halaman profil dalam format Inertia.
     */
    public function edit(Request $request): Response
    {
        // Menampilkan halaman 'Profile/Edit' dengan data apakah email pengguna perlu diverifikasi
        // dan status dari sesi jika ada
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail, // Cek apakah pengguna harus memverifikasi email
            'status' => session('status'), // Ambil status dari sesi untuk ditampilkan
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     *
     * @param \App\Http\Requests\ProfileUpdateRequest $request Objek permintaan validasi profil.
     * @return \Illuminate\Http\RedirectResponse Mengarahkan kembali ke halaman edit profil.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Mengisi data pengguna dengan input yang telah divalidasi
        $request->user()->fill($request->validated());

        // Jika email diubah, reset status verifikasi email
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null; // Set null untuk memaksa pengguna memverifikasi ulang
        }

        // Simpan perubahan profil pengguna
        $request->user()->save();

        // Redirect kembali ke halaman edit profil
        return Redirect::route('profile.edit');
    }

    /**
     * Menghapus akun pengguna.
     *
     * @param \Illuminate\Http\Request $request Objek permintaan pengguna.
     * @return \Illuminate\Http\RedirectResponse Mengarahkan kembali ke halaman utama setelah penghapusan akun.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password pengguna untuk memastikan bahwa mereka benar-benar pemilik akun
        $request->validate([
            'password' => ['required', 'current_password'], // Pastikan password saat ini benar
        ]);

        $user = $request->user(); // Mendapatkan data pengguna saat ini

        // Logout pengguna sebelum menghapus akun
        Auth::logout();

        // Hapus akun pengguna dari database
        $user->delete();

        // Invalidate sesi pengguna saat ini untuk mencegah penggunaan lebih lanjut
        $request->session()->invalidate();

        // Regenerasi token CSRF untuk keamanan tambahan
        $request->session()->regenerateToken();

        // Redirect pengguna ke halaman utama setelah akun dihapus
        return Redirect::to('/');
    }
}
