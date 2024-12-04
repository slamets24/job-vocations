<?php

namespace App\Http\Controllers;

use App\Mail\ApplicantConfirmationMail;
use App\Mail\CompanyApplicationNotificationMail;
use App\Mail\HiredMail;
use App\Mail\InterviewInvitationMail;
use App\Mail\OnReviewNotificationMail;
use App\Mail\PassedTypeStatusMail;
use App\Mail\RejectionNotificationMail;
use App\Mail\TestInvitationMail;
use App\Models\Application;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mendapatkan nilai pencarian dari input request
        $search = $request->input('search');

        // Query untuk mendapatkan data lowongan kerja dengan relasi ke beberapa tabel
        $jobsQuery = DB::table('job_postings')
            ->join('indonesia_provinces', 'job_postings.province_id', '=', 'indonesia_provinces.code')
            ->join('indonesia_cities', 'job_postings.city_id', '=', 'indonesia_cities.code')
            ->join('users', 'job_postings.user_id', '=', 'users.id')
            ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
            ->join('applications', 'applications.job_posting_id', '=', 'job_postings.id')
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
                'applications.status as application_status',
                'job_postings.created_at',
                'company_profiles.image_profile as image',
                'tests.id as test_id'
            )
            ->where('applications.user_id', $user->id) // Filter berdasarkan user yang sedang login
            ->where('job_postings.status', 1) // Filter berdasarkan status aktif
            ->when($search, function ($query) use ($search) {
                // Menambahkan kondisi pencarian jika terdapat input pencarian
                $query->where('job_postings.title', 'like', "%{$search}%");
            })
            ->orderBy('applications.created_at', 'desc'); // Urutkan berdasarkan tanggal lamaran terbaru

        // Paginasi hasil query dengan manipulasi data
        $jobs = $jobsQuery->paginate(10)
            ->appends($request->query()) // Menambahkan query string pada paginasi
            ->through(function ($job) {
                // Mengubah format tanggal menjadi relative time (misalnya "2 hari yang lalu")
                $job->created_at = Carbon::parse($job->created_at)->diffForHumans();
                // Menambahkan URL untuk gambar profil perusahaan atau menggunakan placeholder jika kosong
                $job->image = $job->image ? Storage::url($job->image) : asset('images/placeholder-company.jpg');
                return $job;
            });

        // Mengembalikan data ke view menggunakan Inertia
        return Inertia::render('Personal/Jobs/Dilamar', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Mengambil detail lowongan kerja berdasarkan slug
        $job = DB::table('job_postings')
            ->join('users', 'job_postings.user_id', '=', 'users.id')
            ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
            ->select(
                'job_postings.id',
                'company_profiles.image_profile as image',
                'company_profiles.company_name as company_name',
                'job_postings.title',
                'job_postings.created_at',
                'job_postings.description',
                'job_postings.slug'
            )
            ->where('job_postings.slug', $slug)
            ->first();

        // Menambahkan URL untuk gambar profil perusahaan atau menggunakan placeholder jika kosong
        $job->image = $job->image ? Storage::url($job->image) : asset('images/placeholder-company.jpg');
        // Mengubah format tanggal menjadi relative time
        $job->created_at = Carbon::parse($job->created_at)->diffForHumans();

        // Mengambil data profil pribadi pengguna
        $personal = DB::table('personal_profiles')
            ->join('indonesia_cities', 'personal_profiles.city_id', '=', 'indonesia_cities.code')
            ->join('indonesia_provinces', 'personal_profiles.province_id', '=', 'indonesia_provinces.code')
            ->select(
                'personal_profiles.full_name',
                'personal_profiles.birth_date as birthDate',
                'personal_profiles.address',
                'personal_profiles.phone',
                'personal_profiles.slug',
                'personal_profiles.province_id',
                'personal_profiles.city_id',
                'personal_profiles.district_id',
                'indonesia_cities.name as city_name',
                'indonesia_provinces.name as province_name',
            )
            ->where('user_id', $user->id)
            ->first();

        // Menambahkan format tanggal lahir menjadi format lokal
        $personal->birthDateFormatted = Carbon::parse($personal->birthDate)->translatedFormat('d F Y');

        // Mengembalikan data ke view menggunakan Inertia
        return Inertia::render('Personal/Jobs/Apply', [
            'job' => $job,
            'personal' => $personal,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug)
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();

        // Validasi input resume
        $request->validate([
            'resume' => ['required', 'mimes:pdf', 'max:2048'], // Hanya menerima file PDF dengan ukuran maksimal 2MB
        ], [
            'resume.required' => 'File resume wajib diunggah.',
            'resume.mimes' => 'File resume harus berformat pdf.',
            'resume.max' => 'File resume tidak boleh lebih dari 2 MB.',
        ]);

        DB::beginTransaction();
        try {
            // Mengambil data profil pribadi dan email pengguna
            $personal = DB::table('personal_profiles')
                ->join('users', 'personal_profiles.user_id', '=', 'users.id')
                ->select('personal_profiles.full_name', 'users.email')
                ->where('personal_profiles.user_id', $user->id)
                ->first();

            // Mengambil data lowongan kerja berdasarkan slug
            $job = DB::table('job_postings')
                ->join('users', 'job_postings.user_id', '=', 'users.id')
                ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
                ->select(
                    'job_postings.id',
                    'job_postings.title',
                    'company_profiles.company_name as company_name',
                    'users.email'
                )
                ->where('job_postings.slug', $slug)
                ->first();

            // Jika file resume ada, simpan file ke storage
            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $fileName = Str::ulid() . '.' . $file->getClientOriginalExtension(); // Membuat nama file unik
                $path = $file->storeAs('resumes', $fileName, 'public'); // Menyimpan file ke direktori public/resumes
            }

            // Membuat entri baru di tabel aplikasi lamaran
            $appcationId = DB::table('applications')->insertGetId([
                'user_id' => $user->id,
                'job_posting_id' => $job->id,
                'cv_path' => $path, // Path file resume
                'status' => 'applied', // Status default lamaran
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Menyiapkan data untuk email konfirmasi kepada pelamar
            $applicantData = [
                'subject' => 'Lamaran berhasil dikirim',
                'user' => $personal,
                'job' => $job
            ];

            // Menyiapkan data untuk email notifikasi kepada perusahaan
            $companyData = [
                'subject' => 'Lamaran baru',
                'user' => $personal,
                'job' => $job,
                'files' => [
                    public_path("storage/" . $path) // Menyertakan file resume
                ]
            ];

            DB::commit();

            // Mengirim email ke pelamar dan perusahaan
            Mail::to($personal->email)->send(new ApplicantConfirmationMail($applicantData));
            Mail::to($job->email)->send(new CompanyApplicationNotificationMail($companyData));

            Log::notice("Lamaran berhasil dikirim"); // Log sukses
            return redirect()->route('applications.proposed')->with('success', 'Lamaran berhasil dikirim.');
        } catch (QueryException $e) {
            DB::rollBack(); // Rollback jika ada error pada query
            Log::error("Database Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack(); // Rollback jika ada error umum
            Log::critical("System Error: " . $e->getMessage()); // Log error
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    public function showApplicants($slug)
    {
        // Query untuk mendapatkan data pelamar berdasarkan slug lowongan kerja
        $applicants = DB::table('applications')
            ->join('job_postings', 'applications.job_posting_id', '=', 'job_postings.id') // Menghubungkan aplikasi dengan lowongan kerja
            ->join('users', 'applications.user_id', '=', 'users.id') // Menghubungkan aplikasi dengan data pengguna
            ->join('personal_profiles', 'users.id', '=', 'personal_profiles.user_id') // Menghubungkan pengguna dengan profil pribadi
            ->where('job_postings.slug', $slug) // Filter berdasarkan slug lowongan kerja
            ->where('users.type', 'personal') // Filter hanya untuk pengguna tipe 'personal'
            ->select(
                'users.id as user_id', // ID pengguna
                'users.email as user_email', // Email pengguna
                'personal_profiles.full_name', // Nama lengkap pelamar
                'personal_profiles.address', // Alamat pelamar
                'personal_profiles.phone', // Nomor telepon pelamar
                'personal_profiles.city_id as user_city_id', // ID kota pelamar
                'personal_profiles.province_id as user_province_id', // ID provinsi pelamar
                'job_postings.slug as slugJob', // Slug lowongan kerja
                'personal_profiles.slug as slugApplicant', // Slug profil pelamar
                'job_postings.city_id as job_city_id', // ID kota lowongan kerja
                'job_postings.province_id as job_province_id', // ID provinsi lowongan kerja
                'applications.status', // Status aplikasi
                'applications.cv_path', // Path file CV pelamar
                'applications.created_at as application_date' // Tanggal lamaran
            )
            ->orderByRaw("
            CASE
                WHEN personal_profiles.city_id = job_postings.city_id THEN 1 -- Prioritaskan pelamar dengan kota yang sama
                WHEN personal_profiles.province_id = job_postings.province_id THEN 2 -- Prioritaskan pelamar dengan provinsi yang sama
                ELSE 3 -- Urutkan pelamar lainnya
            END
        ")
            ->orderBy('applications.created_at', 'desc') // Urutkan berdasarkan tanggal lamaran terbaru
            ->get();

        // Mengembalikan data pelamar ke view menggunakan Inertia
        return Inertia::render('Company/Jobs/Applications', [
            'applicants' => $applicants, // Data pelamar
        ]);
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
    public function update(Request $request, string $string)
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

    public function detailApplicant(string $slugJob, string $slugApplicant)
    {
        // Query untuk mendapatkan detail pelamar
        $personal = DB::table('personal_profiles')
            ->join('indonesia_cities', 'personal_profiles.city_id', '=', 'indonesia_cities.code')
            ->join('indonesia_provinces', 'personal_profiles.province_id', '=', 'indonesia_provinces.code')
            ->join('job_classifications', 'personal_profiles.job_classification_id', '=', 'job_vacations.job_classifications.id')
            ->join('users', 'personal_profiles.user_id', '=', 'users.id')
            ->join('applications', 'applications.user_id', '=', 'users.id')
            ->join('job_postings', 'applications.job_posting_id', '=', 'job_postings.id')
            ->join('company_profiles', 'job_postings.user_id', '=', 'company_profiles.user_id')
            ->select(
                'personal_profiles.full_name',
                'personal_profiles.image_profile',
                'personal_profiles.birth_date as birthDate',
                'personal_profiles.address',
                'personal_profiles.biography',
                'personal_profiles.phone',
                'personal_profiles.salary',
                'personal_profiles.province_id',
                'personal_profiles.city_id',
                'personal_profiles.district_id',
                'indonesia_cities.name as city_name',
                'indonesia_provinces.name as province_name',
                'job_classifications.name as job_classification_name',
                'personal_profiles.job_classification_id',
                'applications.id as application_id',
                'applications.cv_path',
                'applications.status',
                'applications.created_at as application_date',
                'users.email',
                'users.id as user_id',
                'job_postings.id as job_posting_id',
                'job_postings.slug as slugJob',
                'job_postings.title',
                'company_profiles.company_name as company_name',
            )
            ->where('personal_profiles.slug', $slugApplicant)
            ->where('job_postings.slug', $slugJob)
            ->first();

        // Cek apakah data ditemukan
        if (!$personal) {
            abort(404, 'Data pelamar tidak ditemukan.');
        }

        // Format ulang data untuk UI
        $personal->image_profile = $personal->image_profile ? Storage::url($personal->image_profile) : asset('images/placeholder-person.jpg');
        $personal->cv_path = $personal->cv_path ? Storage::url($personal->cv_path) : null;
        $personal->birthDateFormatted = Carbon::parse($personal->birthDate)->translatedFormat('d F Y');

        // Update status aplikasi jika status saat ini adalah 'applied'
        if ($personal->status === 'applied') {
            DB::table('applications')
                ->where('user_id', $personal->user_id)
                ->where('job_posting_id', $personal->job_posting_id)
                ->update([
                    'status' => 'under_review',
                    'updated_at' => now(),
                ]);

            // kirim email ke pelamar
            $applicantData = [
                'subject' => 'Lamaran Sedang Di Tinjau',
                'user' => $personal,
            ];

            //Kirim email pemberitahuan
            Mail::to($personal->email)->send(new OnReviewNotificationMail($applicantData));
        }

        return Inertia::render('Company/Jobs/ApplicantDetail', compact('personal'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:applied,under_review,document_rejected,test,test_passed,test_failed,interview,interview_passed,interview_failed,offered,offer_rejected,offer_accepted,hired,rejected', // Validasi status
        ]);

        // Periksa apakah aplikasi ada
        $application = DB::table('applications')->where('id', $id)->first();

        if (!$application) {
            return abort(404, 'Aplikasi tidak ditemukan.');
        }

        $data = DB::table('applications')
            ->join('users', 'applications.user_id', '=', 'users.id')
            ->join('personal_profiles', 'users.id', '=', 'personal_profiles.user_id')
            ->join('job_postings', 'applications.job_posting_id', '=', 'job_postings.id')
            ->join('company_profiles', 'job_postings.user_id', '=', 'company_profiles.user_id')
            ->where('applications.id', $application->id)
            ->select('job_postings.id as job_posting_id', 'personal_profiles.full_name', 'job_postings.title', 'job_postings.slug', 'company_profiles.company_name', 'users.email', 'applications.status')
            ->first();

        // check apakah lowongan ini sudah ada test
        $testExists = DB::table('tests')
            ->where('job_posting_id', $data->job_posting_id)
            ->exists();

        // kirim error jika lowongan ini belum ada test
        if ($request->status === 'test' && !$testExists) {
            return back()->withErrors(['testExists' => 'Lowongan ini belum ada test. Silahkan buat test terlebih dahulu.']);
        }

        // jika status saat ini adalah test_failed dan test_passed, kemudian ingin dirubah menjadi status test maka beri errors
        if($request->status === 'test' && $data->status === 'test_passed' || $data->status === 'test_failed') {
            return back()->withErrors(['hasTest' => 'Pelamar ini sudah melakukan test, tidak bisa test ulang.']);
        }

        // Update status aplikasi
        DB::table('applications')
            ->where('id', $id)
            ->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

        // jika status nya adalah test, maka kirim email pemberitahuan segera isi test
        if ($request->status === 'test') $this->invitationTest($data);

        // jika status nya adalah interview, maka kirim email pemberitahuan lolos ke interview dan menunggu dihubungi di email terpisah
        if ($request->status === 'interview') $this->invitationInterview($data);

        // jika status nya adalah diterima, maka kirim email pemberitahuan diterima
        if ($request->status === 'hired') $this->hired($data);

        // jika status nya adalah tes lulus, interview lulus, ditawari posisi, maka kirim email dan menyuruh untuk menunggu pemberitahuan ke tahap selanjutnya
        if (in_array($request->status, ['test_passed', 'interview_passed', 'offered'])) $this->passedStatus($data);

        // jik status itu berupa rejection seperti document_rejected, test_failed, interview_failed, offer_rejected, rejected. maka kirim email template saja
        if (in_array($request->status, ['document_rejected', 'test_failed', 'interview_failed', 'offer_rejected', 'rejected'])) $this->rejectionStatus($data);

        return redirect()->route('show.applicants', $data->slug)->with('success', 'Berhasil mengubah status.');
    }

    private function invitationTest($data): void
    {
        // kirim email ke pelamar
        $applicantData = [
            'subject' => "Undangan Test Online",
            'user' => (object)[
                'full_name' => $data->full_name,
                'title' => $data->title,
                'company_name' => $data->company_name,
            ],
        ];

        //Kirim email pemberitahuan
        Mail::to($data->email)->send(new TestInvitationMail($applicantData));
    }

    private function invitationInterview($data): void
    {
        // kirim email ke pelamar
        $applicantData = [
            'subject' => "Undangan Interview Online",
            'user' => (object)[
                'full_name' => $data->full_name,
                'title' => $data->title,
                'company_name' => $data->company_name,
            ],
        ];

        //Kirim email pemberitahuan
        Mail::to($data->email)->send(new InterviewInvitationMail($applicantData));
    }

    private function hired($data): void
    {
        // kirim email ke pelamar
        $applicantData = [
            'subject' => "Selamat, Anda Diterima!",
            'user' => (object)[
                'full_name' => $data->full_name,
                'title' => $data->title,
                'company_name' => $data->company_name,
            ],
        ];

        //Kirim email pemberitahuan
        Mail::to($data->email)->send(new HiredMail($applicantData));
    }

    private function rejectionStatus($data): void
    {
        // kirim email ke pelamar
        $applicantData = [
            'subject' => "Tetap Semangat! Terima Kasih atas Lamaran Anda",
            'user' => (object)[
                'full_name' => $data->full_name,
                'title' => $data->title,
                'company_name' => $data->company_name,
            ],
        ];

        //Kirim email pemberitahuan
        Mail::to($data->email)->send(new RejectionNotificationMail($applicantData));
    }

    private function passedStatus($data): void
    {
        // kirim email ke pelamar
        $applicantData = [
            'subject' => "Selamat, Anda Lulus!",
            'user' => (object)[
                'full_name' => $data->full_name,
                'title' => $data->title,
                'company_name' => $data->company_name,
            ],
        ];

        //Kirim email pemberitahuan
        Mail::to($data->email)->send(new PassedTypeStatusMail($applicantData));
    }
}
