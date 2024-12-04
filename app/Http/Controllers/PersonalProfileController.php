<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonalProfileCreateRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\PersonalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PersonalProfileController extends Controller
{
    /**
     * Menampilkan data profil personal pengguna yang sedang login.
     */
    public function index()
    {
        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        // Mengambil data profil pengguna beserta data kota, provinsi, dan klasifikasi pekerjaan
        $personal = DB::table('personal_profiles')
            ->join('indonesia_cities', 'personal_profiles.city_id', '=', 'indonesia_cities.code')
            ->join('indonesia_provinces', 'personal_profiles.province_id', '=', 'indonesia_provinces.code')
            ->join('job_classifications', 'personal_profiles.job_classification_id', '=', 'job_classifications.id')
            ->select(
                'personal_profiles.full_name',
                'personal_profiles.image_profile',
                'personal_profiles.birth_date as birthDate',
                'personal_profiles.address',
                'personal_profiles.biography',
                'personal_profiles.phone',
                'personal_profiles.salary',
                'personal_profiles.slug',
                'personal_profiles.province_id',
                'personal_profiles.city_id',
                'personal_profiles.district_id',
                'indonesia_cities.name as city_name',
                'indonesia_provinces.name as province_name',
                'job_classifications.name as job_classification_name',
                'personal_profiles.job_classification_id'
            )
            ->where('user_id', $user->id)
            ->first();

        // Mengatur URL gambar profil, jika ada
        $personal->image_profile = $personal->image_profile
            ? Storage::url($personal->image_profile)
            : null;

        // Format tanggal lahir ke dalam format lokal
        $personal->birthDateFormatted = Carbon::parse($personal->birthDate)->translatedFormat('d F Y');

        // Mengirim data profil ke halaman Inertia 'Personal/Profile/Show'
        return inertia('Personal/Profile/Show', compact('personal'));
    }

    /**
     * Menampilkan form untuk membuat profil baru.
     */
    public function create()
    {
        // Memeriksa apakah pengguna sudah memiliki profil
        $user = Auth::user();
        $personal = DB::table('personal_profiles')->where('user_id', $user->id)->first();

        // Jika profil sudah ada, redirect ke halaman profil
        if ($personal) {
            return redirect()->route('personal.index');
        }

        // Menampilkan halaman form pembuatan profil
        return Inertia::render('Personal/Profile/Create');
    }

    /**
     * Menyimpan data profil baru ke database.
     */
    public function store(Request $request)
    {
        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        DB::beginTransaction(); // Memulai transaksi database

        try {
            // Memproses file gambar profil jika ada
            if ($request->hasFile('imageProfile')) {
                $file = $request->file('imageProfile');
                $fileName = Str::ulid() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('personal-profiles', $fileName, 'public');
            }

            // Menyimpan data profil ke database
            $personalId = DB::table('personal_profiles')->insertGetId([
                'user_id' => $user->id,
                'image_profile' => $imagePath ?? null,
                'full_name' => $request->input('full_name'),
                'birth_date' => $request->input('birthDate'),
                'address' => $request->input('address'),
                'biography' => $request->input('biography'),
                'phone' => $request->input('phone'),
                'slug' => Str::slug($request->input('full_name')) . '-' . Str::ulid(),
                'province_id' => $request->input('selectedProvince'),
                'city_id' => $request->input('selectedCity'),
                'district_id' => $request->input('selectedDistrict'),
                'job_classification_id' => $request->input('job_classification'),
                'salary' => $request->input('salary'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit(); // Menyelesaikan transaksi

            // Mencatat log sukses
            Log::notice("Personal profile dengan ID $personalId berhasil dibuat!");

            // Redirect ke halaman profil dengan pesan sukses
            return redirect()->route('personal.index')->with('success', 'Profil berhasil dibuat!');
        } catch (QueryException $e) {
            DB::rollBack(); // Membatalkan transaksi jika ada kesalahan query
            Log::error("Database Error: " . $e->getMessage());
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Membatalkan transaksi jika ada kesalahan umum
            Log::critical("System Error: " . $e->getMessage());
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $string)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $string)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $string)
    {
        //
    }

    // Menampilkan halaman dashboard pengguna personal
    public function dashboard()
    {
        // Mengambil data lowongan pekerjaan dari tabel `job_postings`
        // dan menghubungkannya dengan data profil perusahaan dari tabel `company_profiles`
        $jobs = DB::table('job_postings as j')
            ->leftJoin('company_profiles as c', 'j.user_id', '=', 'c.user_id') // Menghubungkan tabel `job_postings` dengan `company_profiles` berdasarkan `user_id`
            ->select(
                'j.*', // Memilih semua kolom dari tabel `job_postings`
                'c.slug as company_slug', // Slug perusahaan
                'c.company_name as company_name', // Nama perusahaan
                'c.image_profile', // Gambar profil perusahaan
            )
            ->get()
            ->map(function ($job) { // Memproses setiap hasil query
                // Menambahkan URL gambar profil perusahaan
                // Jika `image_profile` ada, gunakan URL gambar dari storage
                // Jika tidak ada, gunakan gambar placeholder default
                $job->image_profile = $job->image_profile
                    ? Storage::url($job->image_profile)
                    : asset('images/placeholder-company.jpg');
                return $job; // Mengembalikan data setelah diproses
            });

        // Mengirimkan data `jobs` ke komponen Inertia Vue untuk ditampilkan pada halaman dashboard
        return inertia('Personal/Users/Dashboard', [
            'jobs' => $jobs,
        ]);
    }

    // Mengambil semua data skill dari tabel `skills`
    public function getSkills()
    {
        // Query untuk mendapatkan semua skill dari tabel `skills`
        $skills = DB::table('skills')->get();

        // Mengembalikan data skill dalam format JSON untuk digunakan di frontend
        return response()->json($skills);
    }

    // Mengubah informasi personal pengguna yang terautentikasi
    public function changeInformationCenter(Request $request, string $slug)
    {
        // Mendapatkan data pengguna yang sedang login
        $user = Auth::user();

        // Validasi input dari request
        $validated = $request->validate([
            'full_name' => 'required|string|max:100', // Nama lengkap wajib diisi, maksimal 100 karakter
            'phone' => 'required|string|max:20', // Nomor telepon wajib diisi, maksimal 20 karakter
            'birthDate' => 'required|date', // Tanggal lahir wajib diisi dengan format tanggal yang valid
            'address' => 'required|string|max:255', // Alamat wajib diisi, maksimal 255 karakter
            'selectedProvince' => 'required|exists:indonesia_provinces,code', // Provinsi harus valid sesuai tabel `indonesia_provinces`
            'selectedCity' => 'required|exists:indonesia_cities,code', // Kota/Kabupaten harus valid sesuai tabel `indonesia_cities`
            'selectedDistrict' => 'required|exists:indonesia_districts,code', // Kecamatan harus valid sesuai tabel `indonesia_districts`
        ], [
            // Pesan error kustom untuk validasi input
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.max' => 'Nama lengkap tidak boleh lebih dari 100 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
            'birthDate.required' => 'Tanggal lahir wajib diisi.',
            'birthDate.date' => 'Tanggal lahir harus berupa format tanggal yang valid.',
            'address.required' => 'Alamat wajib diisi.',
            'address.max' => 'Alamat tidak boleh lebih dari 255 karakter.',
            'selectedProvince.required' => 'Provinsi wajib dipilih.',
            'selectedProvince.exists' => 'Provinsi yang dipilih tidak valid.',
            'selectedCity.required' => 'Kota/Kabupaten wajib dipilih.',
            'selectedCity.exists' => 'Kota/Kabupaten yang dipilih tidak valid.',
            'selectedDistrict.required' => 'Kecamatan wajib dipilih.',
            'selectedDistrict.exists' => 'Kecamatan yang dipilih tidak valid.',
        ]);

        // Periksa apakah profil personal dengan slug tertentu ada untuk pengguna ini
        $personalExists = DB::table('personal_profiles')
            ->where('user_id', $user->id) // Filter berdasarkan ID pengguna
            ->where('slug', $slug) // Filter berdasarkan slug profil
            ->exists(); // Mengecek apakah data ada

        // Jika profil tidak ditemukan, kembalikan error
        if (!$personalExists) {
            return back()->withErrors(['notFound' => 'Personal Profile tidak ditemukan!']);
        }

        DB::beginTransaction(); // Memulai transaksi database

        try {
            // Memperbarui data profil personal di database
            DB::table('personal_profiles')
                ->where('user_id', $user->id) // Filter berdasarkan ID pengguna
                ->where('slug', $slug) // Filter berdasarkan slug profil
                ->update([
                    'full_name' => $validated['full_name'], // Perbarui nama lengkap
                    'phone' => $validated['phone'], // Perbarui nomor telepon
                    'birth_date' => $validated['birthDate'], // Perbarui tanggal lahir
                    'address' => $validated['address'], // Perbarui alamat
                    'province_id' => $request->input('selectedProvince'), // Perbarui ID provinsi
                    'city_id' => $request->input('selectedCity'), // Perbarui ID kota/kabupaten
                    'district_id' => $request->input('selectedDistrict'), // Perbarui ID kecamatan
                    'updated_at' => now(), // Perbarui timestamp
                ]);

            DB::commit(); // Menyimpan perubahan ke database
            Log::notice("Personal Profile berhasil diperbarui!"); // Log kesuksesan
            return back()->with('success', 'Profil berhasil diperbarui.'); // Kembalikan respons sukses
        } catch (QueryException $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi kesalahan query
            Log::error("Database Error: " . $e->getMessage()); // Log error database
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi kesalahan umum
            Log::critical("System Error: " . $e->getMessage()); // Log error sistem
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    public function changeBiography(Request $request, string $slug)
    {
        $user = Auth::user(); // Mendapatkan data pengguna yang sedang login

        // Validasi input untuk memastikan biografi wajib diisi dan berupa teks
        $validated = $request->validate([
            'biography' => 'required|string',
        ], [
            'biography.required' => 'Biografi wajib diisi.',
            'biography.string' => 'Biografi harus berupa teks.',
        ]);

        // Memeriksa apakah profil personal pengguna dengan slug tertentu ada
        $personalExists = DB::table('personal_profiles')
            ->where('user_id', $user->id) // Filter berdasarkan ID pengguna
            ->where('slug', $slug) // Filter berdasarkan slug
            ->exists(); // Mengecek keberadaan data

        // Jika profil tidak ditemukan, kirimkan error
        if (!$personalExists) {
            return back()->withErrors(['notFound' => 'Personal Profile tidak ditemukan!']);
        }

        DB::beginTransaction(); // Memulai transaksi database

        try {
            // Memperbarui kolom `biography` pada tabel `personal_profiles`
            DB::table('personal_profiles')
                ->where('user_id', $user->id)
                ->where('slug', $slug)
                ->update([
                    'biography' => $validated['biography'], // Data biografi yang baru
                    'updated_at' => now(), // Timestamp diperbarui
                ]);

            DB::commit(); // Menyimpan perubahan ke database
            Log::notice("Deskripsi Pribadi berhasil diperbarui!"); // Log sukses
            return back()->with('success', 'Deskripsi Pribadi berhasil diperbarui.'); // Kembalikan respons sukses
        } catch (QueryException $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error query
            Log::error("Database Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error umum
            Log::critical("System Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    public function changePhoto(Request $request, string $slug)
    {
        $user = Auth::user(); // Mendapatkan data pengguna yang sedang login

        // Validasi input untuk memastikan file gambar sesuai aturan
        $validated = $request->validate([
            'imageProfile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // File gambar opsional, harus format tertentu, dan maksimal 2MB
        ], [
            'imageProfile.image' => 'File harus berupa gambar.',
            'imageProfile.mimes' => 'Format gambar harus JPEG, PNG, atau JPG.',
            'imageProfile.max' => 'Ukuran file gambar tidak boleh lebih dari 2MB.',
        ]);

        // Memeriksa apakah profil personal pengguna dengan slug tertentu ada
        $personalExists = DB::table('personal_profiles')
            ->where('user_id', $user->id)
            ->where('slug', $slug)
            ->exists();

        // Jika profil tidak ditemukan, kirimkan error
        if (!$personalExists) {
            return back()->withErrors(['notFound' => 'Personal Profile tidak ditemukan!']);
        }

        DB::beginTransaction(); // Memulai transaksi database

        try {
            $file = $validated['imageProfile']; // Mendapatkan file gambar dari request
            $fileName = Str::ulid() . '.' . $file->getClientOriginalExtension(); // Membuat nama file unik
            $imagePath = $file->storeAs('personal-profiles', $fileName, 'public'); // Menyimpan file ke folder `personal-profiles` di storage

            // Memperbarui kolom `image_profile` dengan path gambar yang baru
            DB::table('personal_profiles')
                ->where('user_id', $user->id)
                ->where('slug', $slug)
                ->update([
                    'image_profile' => $imagePath, // Path gambar baru
                    'updated_at' => now(), // Timestamp diperbarui
                ]);

            DB::commit(); // Menyimpan perubahan ke database
            Log::notice("Photo Profile berhasil diperbarui!"); // Log sukses
            return back()->with('success', 'Photo Profile berhasil diperbarui.'); // Kembalikan respons sukses
        } catch (QueryException $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error query
            Log::error("Database Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error umum
            Log::critical("System Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    public function changeClassification(Request $request, string $slug)
    {
        $user = Auth::user(); // Mendapatkan data pengguna yang sedang login

        // Validasi input untuk memastikan klasifikasi pekerjaan diisi dan berupa angka
        $validated = $request->validate([
            'job_classification' => 'required|numeric',
        ], [
            'job_classification.required' => 'Klasifikasi pekerjaan wajib diisi.',
            'job_classification.numeric' => 'Klasifikasi pekerjaan harus berupa angka.',
        ]);

        // Memeriksa apakah profil personal pengguna dengan slug tertentu ada
        $personalExists = DB::table('personal_profiles')
            ->where('user_id', $user->id)
            ->where('slug', $slug)
            ->exists();

        // Jika profil tidak ditemukan, kirimkan error
        if (!$personalExists) {
            return back()->withErrors(['notFound' => 'Personal Profile tidak ditemukan!']);
        }

        DB::beginTransaction(); // Memulai transaksi database

        try {
            // Memperbarui kolom `job_classification_id` pada tabel `personal_profiles`
            DB::table('personal_profiles')
                ->where('user_id', $user->id)
                ->where('slug', $slug)
                ->update([
                    'job_classification_id' => $validated['job_classification'], // ID klasifikasi pekerjaan baru
                    'updated_at' => now(), // Timestamp diperbarui
                ]);

            DB::commit(); // Menyimpan perubahan ke database
            Log::notice("Bidang Pekerjaan berhasil diperbarui!"); // Log sukses
            return back()->with('success', 'Bidang Pekerjaan berhasil diperbarui.'); // Kembalikan respons sukses
        } catch (QueryException $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error query
            Log::error("Database Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi error umum
            Log::critical("System Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    public function changeSalary(Request $request, string $slug)
    {
        $user = Auth::user(); // Mendapatkan informasi pengguna yang saat ini sedang login.

        // Validasi input data yang dikirimkan oleh pengguna.
        $validated = $request->validate([
            'salary' => 'required|numeric', // Field `salary` harus ada dan berupa angka.
        ], [
            'salary.required' => 'Gaji wajib diisi.', // Pesan error jika `salary` kosong.
            'salary.numeric' => 'Gaji harus berupa angka.', // Pesan error jika `salary` bukan angka.
        ]);

        // Memeriksa apakah profil personal dengan slug tertentu milik user login ada di database.
        $personalExists = DB::table('personal_profiles')
            ->where('user_id', $user->id) // Filter berdasarkan ID pengguna.
            ->where('slug', $slug) // Filter berdasarkan slug profil.
            ->exists(); // Mengembalikan true jika profil ditemukan, false jika tidak.

        if (!$personalExists) {
            // Jika profil tidak ditemukan, kembalikan ke halaman sebelumnya dengan pesan error.
            return back()->withErrors(['notFound' => 'Personal Profile tidak ditemukan!']);
        }

        DB::beginTransaction(); // Memulai transaksi database untuk memastikan perubahan data aman.

        try {
            // Memperbarui gaji pada profil personal di database.
            DB::table('personal_profiles')
                ->where('user_id', $user->id) // Hanya profil milik pengguna login yang diperbarui.
                ->where('slug', $slug) // Berdasarkan slug yang diberikan.
                ->update([
                    'salary' => $validated['salary'], // Memperbarui kolom `salary` dengan nilai yang divalidasi.
                    'updated_at' => now(), // Mengupdate timestamp `updated_at` dengan waktu saat ini.
                ]);

            DB::commit(); // Menyimpan perubahan ke database jika tidak ada error.
            Log::notice("Harapan Gaji berhasil diperbarui!"); // Mencatat log sukses.
            return back()->with('success', 'Harapan Gaji berhasil diperbarui.'); // Mengembalikan pesan sukses ke pengguna.

        } catch (QueryException $e) {
            DB::rollBack(); // Membatalkan semua perubahan jika terjadi error pada query database.
            Log::error("Database Error: " . $e->getMessage()); // Mencatat error ke log.
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput(); // Mengembalikan pesan error ke pengguna.
        } catch (Exception $e) {
            DB::rollBack(); // Membatalkan semua perubahan jika terjadi error sistem.
            Log::critical("System Error: " . $e->getMessage()); // Mencatat error sistem ke log.
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput(); // Mengembalikan pesan error umum ke pengguna.
        }
    }
}
