<?php

namespace App\Http\Controllers;

// Import library dan kelas yang digunakan dalam controller
use App\Http\Requests\CompanyProfileCreateRequest; // Validasi untuk pembuatan profil perusahaan
use App\Http\Requests\CompanyProfileUpdateRequest; // Validasi untuk pembaruan profil perusahaan
use Exception; // Untuk menangani general error
use Illuminate\Database\QueryException; // Untuk menangani error query database
use Illuminate\Support\Facades\Log; // Untuk mencatat log
use Inertia\Inertia; // Digunakan untuk render halaman dengan Inertia.js
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Untuk manipulasi string (e.g., slug, ulid)
use Illuminate\Support\Facades\Auth; // Untuk autentikasi pengguna
use Illuminate\Support\Facades\DB; // Untuk menjalankan query database
use Illuminate\Support\Facades\Storage; // Untuk pengelolaan file di storage
use Carbon\Carbon; // Untuk manipulasi waktu

class CompanyProfileController extends Controller
{
    /**
     * Menampilkan halaman profil perusahaan pengguna yang sedang login.
     */
    public function index()
    {
        // Mendapatkan data pengguna yang sedang login
        $user = Auth::user();

        // Query untuk mendapatkan data profil perusahaan, termasuk provinsi dan kota
        $company = DB::table('company_profiles as c')
            ->leftJoin('indonesia_provinces as p', 'c.province_id', '=', 'p.code')
            ->leftJoin('indonesia_cities as ct', 'c.city_id', '=', 'ct.code')
            ->leftJoin('indonesia_districts as d', 'c.district_id', '=', 'd.code')
            ->where('c.user_id', $user->id)
            ->select(
                'c.id',
                'c.image_profile',
                'c.company_name',
                'c.company_address',
                'c.description',
                'c.social_media',
                'c.slug',
                'ct.name as city_name',
                'p.name as province_name'
            )
            ->first();

        // Menambahkan URL gambar profil atau placeholder jika tidak ada gambar
        $company->image_profile = $company->image_profile ? Storage::url($company->image_profile) : asset('images/placeholder-company.jpg');

        // Render halaman detail profil perusahaan menggunakan Inertia.js
        return Inertia::render('Company/Profile/Detail', compact('company'));
    }

    /**
     * Menampilkan form untuk membuat profil perusahaan baru.
     */
    public function create()
    {
        $user = Auth::user();

        // Mengecek apakah perusahaan sudah memiliki profil
        $company = DB::table('company_profiles')->where('user_id', $user->id)->first();

        if ($company) {
            // Jika profil sudah ada, redirect ke halaman profil perusahaan
            return redirect()->route('company.profile.index');
        }

        // Render halaman pembuatan profil perusahaan
        return Inertia::render('Company/Profile/Create');
    }

    /**
     * Menyimpan data profil perusahaan baru ke dalam database.
     */
    public function store(CompanyProfileCreateRequest $request)
    {
        $user = Auth::user();

        DB::beginTransaction(); // Memulai transaksi database
        try {
            // Jika ada file gambar yang diunggah, simpan file tersebut
            if ($request->hasFile('imageProfile')) {
                $file = $request->file('imageProfile');
                $fileName = Str::ulid() . '.' . $file->getClientOriginalExtension(); // Generate nama file unik
                $imagePath = $file->storeAs('company-profiles', $fileName, 'public'); // Simpan di storage
            }

            // Menyimpan data profil perusahaan ke dalam tabel `company_profiles`
            $companyId = DB::table('company_profiles')->insertGetId([
                'user_id' => $user->id,
                'image_profile' => $imagePath ?? null,
                'company_name' => $request->input('companyName'),
                'company_address' => $request->input('companyAddress'),
                'description' => $request->input('description'),
                'social_media' => $request->input('media'),
                'slug' => Str::slug($request->input('companyName')), // Generate slug
                'province_id' => $request->input('selectedProvince'),
                'city_id' => $request->input('selectedCity'),
                'district_id' => $request->input('selectedDistrict'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit(); // Komit transaksi
            Log::notice("Company profile dengan ID $companyId berhasil dibuat!");
            return redirect()->route('company.profile.index')->with('success', 'Company profile berhasil dibuat!');
        } catch (QueryException $e) {
            DB::rollBack(); // Kembalikan perubahan jika ada error pada query
            Log::error("Database Error: " . $e->getMessage());
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Kembalikan perubahan jika terjadi error umum
            Log::critical("System Error: " . $e->getMessage());
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    /**
     * Menampilkan halaman edit profil perusahaan berdasarkan slug.
     */
    public function edit(string $slug)
    {
        $user = Auth::user();

        // Query untuk mendapatkan data profil perusahaan
        $company = DB::table('company_profiles')
            ->select('id', 'image_profile', 'company_name', 'company_address', 'description', 'social_media', 'province_id', 'city_id', 'district_id')
            ->where('slug', $slug)
            ->where('user_id', $user->id)
            ->first();

        // Menambahkan URL gambar profil atau placeholder jika tidak ada gambar
        $company->image_profile = $company->image_profile ? Storage::url($company->image_profile) : asset('images/placeholder-company.jpg');

        // Render halaman edit profil perusahaan menggunakan Inertia.js
        return Inertia::render('Company/Profile/Edit', compact('company'));
    }

    /**
     * Memperbarui data profil perusahaan yang sudah ada di database.
     */
    public function update(CompanyProfileUpdateRequest $request, string $id)
    {
        DB::beginTransaction(); // Memulai transaksi database
        try {
            // Mendapatkan data lama perusahaan
            $company = DB::table('company_profiles')
                ->where('id', $id)
                ->select('company_name', 'image_profile')
                ->first();

            // Menyiapkan data yang akan diperbarui
            $updateData = [
                'company_name' => $request->input('companyName'),
                'company_address' => $request->input('companyAddress'),
                'social_media' => $request->input('media'),
                'slug' => Str::slug($request->input('companyName')), // Generate slug baru
                'province_id' => $request->input('selectedProvince'),
                'city_id' => $request->input('selectedCity'),
                'district_id' => $request->input('selectedDistrict'),
                'description' => $request->input('description'),
                'updated_at' => now(),
            ];

            // Jika ada file gambar baru yang diunggah, simpan file tersebut
            if ($request->hasFile('imageProfile')) {
                $file = $request->file('imageProfile');
                $fileName = Str::ulid() . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('company-profiles', $fileName, 'public');

                // Hapus gambar lama jika ada
                if ($company->image_profile) {
                    Storage::disk('public')->delete($company->image_profile);
                }

                $updateData['image_profile'] = $imagePath;
            }

            // Perbarui data di tabel `company_profiles`
            DB::table('company_profiles')
                ->where('id', $id)
                ->update($updateData);

            DB::commit(); // Komit transaksi
            Log::notice("Company Profil dengan ID $id berhasil diperbarui!");
            return redirect()->route('company.profile.index')->with('success', 'Profil berhasil diperbarui.');
        } catch (QueryException $e) {
            DB::rollBack(); // Kembalikan perubahan jika ada error pada query
            Log::error("Database Error: " . $e->getMessage());
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Kembalikan perubahan jika terjadi error umum
            Log::critical("System Error: " . $e->getMessage());
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    /**
     * Fungsi tambahan: Memeriksa apakah profil pengguna sudah lengkap.
     */
    public function checkProfile(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $userId = $user->id;

        // Mendapatkan data profil pengguna berdasarkan tipe (company atau personal)
        $profile = $this->getUserProfile($user->type, $userId);

        // Mengecek apakah profil sudah lengkap
        $isProfileFilled = $this->isProfileComplete($profile, $user->type);

        return response()->json([
            'message' => $isProfileFilled ? 'Profile sudah diisi.' : 'Profile belum lengkap. Silakan lengkapi data profil Anda.',
            'profile_filled' => $isProfileFilled,
        ]);
    }

    /**
     * Mendapatkan data profil berdasarkan tipe pengguna (company/personal).
     */
    private function getUserProfile(string $userType, int $userId)
    {
        if ($userType === 'company') {
            return DB::table('company_profiles')
                ->where('user_id', $userId)
                ->select('image_profile', 'company_name', 'company_address', 'description', 'province_id', 'city_id', 'district_id')
                ->first();
        }

        return DB::table('personal_profiles')
            ->where('user_id', $userId)
            ->select('image_profile', 'full_name', 'address', 'birth_date', 'phone', 'province_id', 'city_id', 'district_id')
            ->first();
    }

    /**
     * Mengecek apakah data profil sudah lengkap berdasarkan tipe pengguna.
     */
    private function isProfileComplete($profile, string $userType): bool
    {
        if (!$profile) {
            return false; // Jika profil kosong, dianggap tidak lengkap
        }

        // Menentukan field yang diperlukan untuk masing-masing tipe pengguna
        $requiredFields = $userType === 'company'
            ? ['image_profile', 'company_name', 'description', 'company_address', 'province_id', 'city_id', 'district_id']
            : ['image_profile', 'full_name', 'address', 'birth_date', 'phone', 'province_id', 'city_id', 'district_id'];

        // Cek apakah semua field yang diperlukan terisi
        foreach ($requiredFields as $field) {
            if (empty($profile->{$field})) {
                return false;
            }
        }

        return true; // Jika semua field terisi, profil dianggap lengkap
    }

    /**
     * Menampilkan dashboard perusahaan dengan statistik pekerjaan.
     */
    public function dashboard()
    {
        $userId = Auth::user()->id;

        // Hitung jumlah pekerjaan yang diposting oleh perusahaan
        $totalPostedJobs = DB::table('job_postings')
            ->where('user_id', $userId)
            ->count();

        // Hitung jumlah pekerjaan yang sudah ditutup
        $totalClosedJobs = DB::table('job_postings')
            ->where('user_id', $userId)
            ->where('status', '0')
            ->count();

        // Hitung jumlah pekerjaan yang sudah diambil pelamar
        $totalTakenJobs = DB::table('applications')
            ->join('job_postings', 'applications.job_posting_id', '=', 'job_postings.id')
            ->where('job_postings.user_id', $userId)
            ->count();

        // Mendapatkan data pekerjaan yang diposting perusahaan
        $jobs = DB::table('job_postings')
            ->where('user_id', $userId)
            ->get();

        // Render halaman dashboard perusahaan menggunakan Inertia.js
        return Inertia::render('Company/Dashboard/Dashboard', compact('jobs', 'totalPostedJobs', 'totalClosedJobs', 'totalTakenJobs'));
    }
}
