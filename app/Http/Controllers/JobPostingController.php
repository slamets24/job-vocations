<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobCreateRequest;
use App\Http\Requests\JobUpdateRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    /**
     * Menampilkan daftar lowongan pekerjaan.
     * Memungkinkan pencarian berdasarkan judul lowongan.
     */
    public function index(Request $request)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mendapatkan parameter pencarian dari request
        $search = $request->input('search');

        // Query untuk mengambil data lowongan pekerjaan dengan join ke tabel terkait
        $jobsQuery = DB::table('job_postings')
            ->join('indonesia_provinces', 'job_postings.province_id', '=', 'indonesia_provinces.code')
            ->join('indonesia_cities', 'job_postings.city_id', '=', 'indonesia_cities.code')
            ->join('users', 'job_postings.user_id', '=', 'users.id')
            ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
            ->leftJoin('tests', 'job_postings.id', '=', 'tests.job_posting_id')
            ->select(
                'job_postings.id',
                'job_postings.title',
                'company_profiles.company_name as company_name',
                'indonesia_provinces.name as province_name',
                'indonesia_cities.name as city_name',
                'job_postings.slug',
                'job_postings.salary',
                'job_postings.status',
                'job_postings.created_at',
                'company_profiles.image_profile as image',
                DB::raw('CASE WHEN tests.id IS NOT NULL THEN true ELSE false END as has_tests')
            )
            // Filter hanya lowongan yang dibuat oleh user yang sedang login
            ->where('job_postings.user_id', $user->id)
            // Menambahkan filter pencarian jika parameter "search" ada
            ->when($search, function ($query) use ($search) {
                $query->where('job_postings.title', 'like', "%{$search}%");
            })
            ->orderBy('job_postings.created_at', 'desc'); // Urutkan berdasarkan tanggal pembuatan terbaru

        // Paginasi data hasil query, sekaligus format data untuk tampilan
        $jobs = $jobsQuery->paginate(10)
            ->appends($request->query()) // Tambahkan parameter query ke URL paginasi
            ->through(function ($job) {
                // Format tanggal menjadi relative (e.g., "2 jam yang lalu")
                $job->created_at = Carbon::parse($job->created_at)->diffForHumans();
                // Tambahkan URL ke gambar perusahaan atau gambar placeholder
                $job->image = $job->image ? Storage::url($job->image) : asset('images/placeholder-company.jpg');
                return $job;
            });

        // Kirim data ke view dengan Inertia
        return inertia('Company/Jobs/Index', compact('jobs', 'search'));
    }

    /**
     * Menampilkan halaman untuk membuat lowongan pekerjaan baru.
     */
    public function create()
    {
        return inertia('Company/Jobs/Create');
    }

    /**
     * Menyimpan data lowongan pekerjaan baru.
     */
    public function store(JobCreateRequest $request)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        DB::beginTransaction();
        try {
            // Mendapatkan lokasi berdasarkan input atau lokasi perusahaan
            $selectedProvince = $request->input('selectedProvince');
            $selectedCity = $request->input('selectedCity');
            $selectedDistrict = $request->input('selectedDistrict');

            if ($request->input('is_same_location')) {
                // Jika lokasi sama dengan lokasi perusahaan
                $company = DB::table('company_profiles')
                    ->select('province_id', 'city_id', 'district_id')
                    ->where('user_id', $user->id)
                    ->first();

                $selectedProvince = $company->province_id;
                $selectedCity = $company->city_id;
                $selectedDistrict = $company->district_id;
            }

            // Simpan data lowongan ke database
            $jobId = DB::table('job_postings')->insertGetId([
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'salary' => $request->input('salary'),
                'education' => $request->input('education'),
                'slug' => Str::slug($request->input('title')) . '-' . Str::ulid(), // Membuat slug unik
                'job_type' => $request->input('job_type'),
                'province_id' => $selectedProvince,
                'city_id' => $selectedCity,
                'district_id' => $selectedDistrict,
                'created_at' => now(),
                'updated_at' => now(),
                'job_classification_id' => $request->input('job_classification'),
            ]);

            DB::commit();
            Log::notice("Lowongan kerja dengan ID $jobId berhasil dibuat!");
            return redirect()->route('jobs.index')->with('success', 'Lowongan kerja berhasil dibuat!');
        } catch (QueryException $e) {
            // Rollback transaksi jika terjadi error database
            DB::rollBack();
            Log::error("Database Error: " . $e->getMessage());
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi error umum
            DB::rollBack();
            Log::critical("System Error: " . $e->getMessage());
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    /**
     * Menampilkan halaman untuk mengedit lowongan pekerjaan berdasarkan slug.
     */
    public function edit(string $slug)
    {
        // Mengambil data lowongan berdasarkan slug
        $job = DB::table('job_postings')
            ->select('id', 'title', 'description', 'salary', 'slug', 'education', 'job_type', 'status', 'province_id', 'city_id', 'district_id', 'job_classification_id')
            ->where('slug', $slug)
            ->first();

        // Jika tidak ditemukan, tampilkan halaman 404
        if (!$job) {
            abort(404);
        }

        // Kirim data ke view untuk diedit
        return inertia('Company/Jobs/Edit', compact('job'));
    }

    /**
     * Menampilkan detail lowongan pekerjaan berdasarkan slug.
     */
    public function show(string $slug)
    {
        // Query untuk mendapatkan detail lowongan pekerjaan
        $job = DB::table('job_postings')
            ->join('job_classifications', 'job_postings.job_classification_id', '=', 'job_classifications.id')
            ->join('indonesia_provinces', 'job_postings.province_id', '=', 'indonesia_provinces.code')
            ->join('indonesia_cities', 'job_postings.city_id', '=', 'indonesia_cities.code')
            ->join('users', 'job_postings.user_id', '=', 'users.id')
            ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
            ->select(
                'job_postings.id',
                'job_postings.title',
                'job_postings.description',
                'job_postings.slug',
                'job_postings.education',
                'job_postings.job_type',
                'job_postings.status',
                'job_postings.salary',
                'job_postings.created_at',
                'indonesia_provinces.name as province_name',
                'indonesia_cities.name as city_name',
                'company_profiles.image_profile as image',
                'company_profiles.company_name as company_name',
                'company_profiles.slug as company_slug',
                'job_classifications.name as job_classification_name'
            )
            ->where('job_postings.slug', $slug)
            ->first();

        // Tampilkan halaman 404 jika lowongan tidak ditemukan
        if (!$job) {
            abort(404);
        }

        // Format ulang data untuk tampilan
        $job->image = $job->image ? Storage::url($job->image) : asset('images/placeholder-company.jpg');
        $job->created_at = Carbon::parse($job->created_at)->diffForHumans();

        // Cek status aplikasi pengguna yang sedang login
        if (Auth::check()) {
            $application = DB::table('applications')
                ->where('user_id', Auth::user()->id)
                ->where('job_posting_id', $job->id)
                ->first();

            $job->application_status = $application ? $application->status : null;
        }

        return inertia('Company/Jobs/Show', compact('job'));
    }

    public function update(JobUpdateRequest $request, string $id)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mengambil data lowongan berdasarkan ID
        $job = DB::table('job_postings')->where('id', $id)->first();

        // Jika lowongan tidak ditemukan atau tidak diakses oleh user yang sesuai, kembalikan dengan error
        if (!$job) {
            return back()->withErrors(['notFound' => 'Lowongan kerja tidak ditemukan atau Anda tidak memiliki akses.']);
        }

        DB::beginTransaction(); // Mulai transaksi untuk memastikan konsistensi data
        try {
            // Mendapatkan lokasi yang dipilih dari request
            $selectedProvince = $request->input('selectedProvince');
            $selectedCity = $request->input('selectedCity');
            $selectedDistrict = $request->input('selectedDistrict');

            // Jika lokasi sama dengan lokasi perusahaan, gunakan lokasi perusahaan
            if ($request->input('is_same_location')) {
                $company = DB::table('company_profiles')
                    ->select('province_id', 'city_id', 'district_id')
                    ->where('user_id', $user->id)
                    ->first();

                $selectedProvince = $company->province_id;
                $selectedCity = $company->city_id;
                $selectedDistrict = $company->district_id;
            }

            // Menentukan slug baru jika judul lowongan diubah
            $newTitle = $request->input('title');
            $slug = $job->title !== $newTitle
                ? Str::slug($newTitle) . '-' . Str::ulid() // Membuat slug baru dengan ulid unik
                : $job->slug;

            // Update data lowongan di database
            DB::table('job_postings')
                ->where('id', $id)
                ->update([
                    'title' => $newTitle,
                    'description' => $request->input('description'),
                    'salary' => $request->input('salary'),
                    'education' => $request->input('education'),
                    'slug' => $slug,
                    'status' => $request->input('is_closed') ? '0' : '1', // Update status berdasarkan input
                    'job_type' => $request->input('job_type'),
                    'province_id' => $selectedProvince,
                    'city_id' => $selectedCity,
                    'district_id' => $selectedDistrict,
                    'job_classification_id' => $request->input('job_classification'),
                    'updated_at' => now(), // Update timestamp
                ]);

            DB::commit(); // Commit transaksi jika semua berhasil
            Log::notice("Lowongan kerja dengan ID $id berhasil diperbarui!");
            return redirect()->route('jobs.index')->with('success', 'Lowongan kerja berhasil diperbarui!');
        } catch (QueryException $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error query
            Log::error("Database Error: " . $e->getMessage());
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error umum
            Log::critical("System Error: " . $e->getMessage());
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    public function destroy(string $id)
    {
        DB::beginTransaction(); // Mulai transaksi untuk penghapusan data
        try {
            // Menghapus lowongan kerja berdasarkan ID
            DB::table('job_postings')
                ->where('id', $id)
                ->delete();

            DB::commit(); // Commit transaksi jika berhasil
            Log::notice("Lowongan kerja dengan ID $id berhasil dihapus!");
            return redirect()->route('jobs.index')->with('success', 'Lowongan kerja berhasil dihapus!');
        } catch (QueryException $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error query
            Log::error('Database error: ' . $e->getMessage());
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!']);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback transaksi jika terjadi error umum
            Log::critical('System error: ' . $e->getMessage());
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!']);
        }
    }

    public function getClassifications()
    {
        // Mengambil semua data klasifikasi pekerjaan dari tabel job_classifications
        $jobsClassifications = DB::table('job_classifications')->get();

        // Mengembalikan data dalam bentuk JSON untuk API atau Ajax
        return response()->json($jobsClassifications);
    }
}
