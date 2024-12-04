<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\DomicileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\PersonalProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExamController;


Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/check-profile', [CompanyProfileController::class, 'checkProfile'])->name('check.profile');
    Route::get('/locations/cities/by-name', [DomicileController::class, 'getCitiesByName'])->name('locations.cities.byName');
    Route::get('/locations/provinces', [DomicileController::class, 'getProvinces'])->name('locations.provinces');
    Route::get('/locations/cities/{provinceId}', [DomicileController::class, 'getCities'])->name('locations.cities');
    Route::get('/locations/districts/{cityId}', [DomicileController::class, 'getDistricts'])->name('locations.districts');
    Route::get('/jobs/classifications', [JobPostingController::class, 'getClassifications'])->name('jobs.classifications');
    Route::get('/getSkill', [PersonalProfileController::class, 'getSkills'])->name('skills.index');
});

// Mencari lowongan pekerjaan. Memungkinkan pengguna mencari lowongan yang sesuai dengan kriteria mereka.
Route::get('/', [HomeController::class, 'index'])->name('home');

// Untuk menampilkan halaman yang berisi syarat dan ketentuan.
Route::get('/terms', function () {
    return Inertia::render('TermCondition');
})->name('terms');

// Untuk menampilkan halaman yang berisi kebijakan privasi dan keamanan.
Route::get('/security-privacy', function () {
    return Inertia::render('SecurityAndPrivacy');
})->name('security-privacy');


Route::get('/dashboard', [CompanyProfileController::class, 'dashboard'])->middleware(['roles:company', 'verified', 'check.profile'])->name('company.dashboard');

// JOBS ROUTES
// Mengelompokkan semua route yang berkaitan dengan fitur lowongan pekerjaan
Route::middleware(['roles:company', 'verified', 'check.profile'])->prefix('jobs')->group(function () {
    // Menampilkan lowongan yang sudah di posting oleh pemberi lowongan/company/perusahaan
    Route::get('/', [JobPostingController::class, 'index'])->name('jobs.index');

    // Menampilkan formulir untuk membuat lowongan baru. Memungkinkan pengguna 'company' untuk membuat lowongan pekerjaan baru
    Route::get('/create', [JobPostingController::class, 'create'])->name('jobs.create');

    // Menyimpan data lowongan baru ke dalam database
    Route::post('/', [JobPostingController::class, 'store'])->name('jobs.store');

    // Menampilkan formulir untuk mengedit lowongan yang sudah ada. Memungkinkan pengguna 'company' untuk mengubah data lowongan yang sudah ada
    Route::get('/{slug}/edit', [JobPostingController::class, 'edit'])->name('jobs.edit');

    // Memperbarui data lowongan yang sudah ada.
    Route::put('/{slug}', [JobPostingController::class, 'update'])->name('jobs.update');

    // Menampilkan daftar lowongan yang telah diikuti oleh pengguna. Dapat diakses oleh pengguna dengan peran 'personal'
    Route::get('/applied', [ApplicationController::class, 'index'])
        ->middleware(['roles:personal'])
        ->withoutMiddleware('roles:company')
        ->name('applications.proposed');

    // Menampilkan detail lowongan. Memberikan informasi lengkap tentang sebuah lowongan kepada pengguna. Dapat diakses oleh pengguna dengan peran 'company' atau 'personal'.
    Route::get('/{slug}', [JobPostingController::class, 'show'])
        ->withoutMiddleware(['roles:company', 'verified', 'check.profile'])
        ->name('jobs.show');

    // Menghapus lowongan dari database. Hanya dapat diakses oleh pengguna dengan peran 'company'
    Route::delete('/{jobId}', [JobPostingController::class, 'destroy'])->name('jobs.destroy');

    // Memperbarui status lamaran. Hanya dapat diakses oleh pengguna dengan peran 'company'
    Route::post('/applications/{id}/status', [ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');

    // Menampilkan formulir untuk melamar lowongan. Dapat diakses oleh pengguna dengan peran 'personal'
    Route::get('/{slug}/apply', [ApplicationController::class, 'create'])
        ->middleware(['roles:personal', 'check.job.application'])
        ->withoutMiddleware('roles:company')
        ->name('applications.create');

    // Memasukan data lamaran, Dapat diakses oleh penggunan dengan peran 'personal'
    Route::post('/{slug}/apply', [ApplicationController::class, 'store'])
        ->middleware(['roles:personal', 'check.job.application'])
        ->withoutMiddleware('roles:company')
        ->name('applications.store');

    // Company melihat pelamar-pelamar yang ada
    Route::get('/check/{slug}', [ApplicationController::class, 'showApplicants'])->name('show.applicants');

    // company melihat detail pelamar
    Route::get('/{slugJob}/{slugApplicant}', [ApplicationController::class, 'detailApplicant'])
        ->name('applications.detailApplicant');
});


// COMPANY ROUTES
// Kode routing di bawah mengelompokkan semua rute yang berkaitan dengan perusahaan menjadi satu grup dengan prefix companies.
Route::prefix('companies')->group(function () {

    // Untuk menampilkan halaman hasi dari pencarian
    Route::get('/search', [CompanyController::class, 'search'])->name('companies.search');

    // Untuk menampilkan halaman yang berisi daftar semua perusahaan.
    Route::get('/', [CompanyController::class, 'index'])->name('companies.index');

    // Untuk menampilkan halaman yang berisi detail sebuah perusahaan berdasarkan slug-nya.
    Route::get('/{slug}', [CompanyController::class, 'show'])->name('companies.show');
});

// PROFIL ROUTES
// Kode routing di bawah mengelompokkan semua rute yang berhubungan dengan profil pengguna menjadi satu grup dengan prefix profiles.
// Kemudian, grup ini dibagi lagi menjadi dua sub-grup berdasarkan peran pengguna: company dan personal. Setiap sub-grup memiliki rute-rute yang spesifik untuk masing-masing peran.
Route::prefix('profiles')->group(function () {

    // Mengelompokkan semua rute yang berkaitan dengan profil perusahaan. Memisahkan logika routing untuk profil perusahaan dan personal.
    Route::prefix('company')->middleware(['roles:company', 'verified', 'check.profile'])->group(function () {

        // Menampilkan halaman profil perusahaan.
        Route::get('/', [CompanyProfileController::class, 'index'])->name('company.profile.index');

        // Menampilkan formulir untuk mengedit profil perusahaan.
        Route::get('/{slug}/edit', [CompanyProfileController::class, 'edit'])->name('company.profile.edit');

        // Memperbarui data profil perusahaan
        Route::put('/{id}', [CompanyProfileController::class, 'update'])->name('company.profile.update');

        Route::withoutMiddleware('check.profile')->group(function () {
            // Menampilkan formulir untuk membuat profil perusahaan (tanpa middleware check.profile)
            Route::get('/create', [CompanyProfileController::class, 'create'])->name('company.profile.create');

            // Menyimpan data profil perusahaan yang baru dibuat.
            Route::post('/', [CompanyProfileController::class, 'store'])->name('company.profile.store');
        });
    });

    // Mengelompokkan semua rute yang berkaitan dengan profil pribadi. Memisahkan logika routing untuk profil perusahaan dan personal.
    Route::prefix('personal')->middleware(['roles:personal', 'verified'])->group(function () {

        // Menampilkan halaman profil pribadi (dengan middleware check.profile).
        Route::get('/', [PersonalProfileController::class, 'index'])->middleware('check.profile')->name('personal.index');

        // Menampilkan formulir untuk membuat profil pribadi.
        Route::get('/create', [PersonalProfileController::class, 'create'])->name('personal.create');

        // Menyimpan data profil pribadi yang baru dibuat.
        Route::post('/', [PersonalProfileController::class, 'store'])->name('personal.store');

        // Mengubah personal profil spesifik beberapa kolom utama saja
        Route::put('/{slug}/information', [PersonalProfileController::class, 'changeInformationCenter'])->name('personal.center.update');

        // Mengubah biography/deskripsi pribadi
        Route::put('/{slug}/biography', [PersonalProfileController::class, 'changeBiography'])->name('personal.biography.update');

        // Mengubah photo profile
        Route::put('/{slug}/photo', [PersonalProfileController::class, 'changePhoto'])->name('personal.photo.update');

        // Mengubah Bidang Pekerjaan
        Route::put('/{slug}/classification', [PersonalProfileController::class, 'changeClassification'])->name('personal.classification.update');

        // Mengubah Bidang Pekerjaan
        Route::put('/{slug}/salary', [PersonalProfileController::class, 'changeSalary'])->name('personal.salary.update');
    });
});

// CBT ROUTES
// Kode routing di bawah mengelompokkan semua rute yang berkaitan dengan ujian (tests) menjadi satu grup dengan prefix 'tests'.
Route::middleware(['roles:company', 'verified'])->prefix('tests')->group(function () {
    // Rute ini digunakan untuk menampilkan halaman pembuatan ujian baru berdasarkan 'slug'.
    // Middleware memastikan hanya pengguna dengan peran 'company' dan yang sudah terverifikasi yang bisa mengaksesnya.
    Route::get('/create/{slug}', [ExamController::class, 'create'])->name('cbt.create');

    // Rute ini digunakan untuk menyimpan ujian yang baru dibuat ke dalam database.
    // Middleware memastikan hanya pengguna dengan peran 'company' dan yang sudah terverifikasi yang bisa mengaksesnya.
    Route::post('/', [ExamController::class, 'store'])->name('cbt.store');

    // Rute ini digunakan untuk menampilkan detail ujian berdasarkan 'slug'.
    // Middleware 'roles:company' dihapus, hanya pengguna dengan peran 'personal' yang bisa mengaksesnya.
    Route::get('/{slug}', [ExamController::class, 'show'])
        ->withoutMiddleware('roles:company')  // Menonaktifkan middleware 'roles:company' untuk rute ini.
        ->middleware('roles:personal')  // Memastikan hanya pengguna dengan peran 'personal' yang bisa mengaksesnya.
        ->name('cbt.show');

    // Rute ini digunakan untuk mengirimkan jawaban ujian yang telah dilakukan oleh peserta ujian.
    // Middleware 'roles:company' dihapus, hanya pengguna dengan peran 'personal' yang bisa mengaksesnya.
    Route::post('/submit-answers', [ExamController::class, 'submitAnswers'])
        ->withoutMiddleware('roles:company')  // Menonaktifkan middleware 'roles:company' untuk rute ini.
        ->middleware('roles:personal')  // Memastikan hanya pengguna dengan peran 'personal' yang bisa mengaksesnya.
        ->name('cbt.submit.answers');

    // Rute ini digunakan untuk menghapus ujian berdasarkan ID.
    // Hanya pengguna dengan peran yang sesuai yang dapat mengaksesnya, yang ditentukan oleh middleware default 'roles:company' dan 'verified'.
    Route::delete('/{id}', [ExamController::class, 'destroy'])->name('cbt.destroy');

    // Rute ini digunakan untuk memperbarui ujian berdasarkan ID.
    // Hanya pengguna dengan peran yang sesuai yang dapat mengaksesnya, yang ditentukan oleh middleware default 'roles:company' dan 'verified'.
    Route::put('/{id}', [ExamController::class, 'update'])->name('cbt.update');

    // Rute ini digunakan untuk menampilkan halaman edit ujian berdasarkan 'slug'.
    // Middleware memastikan hanya pengguna dengan peran 'company' dan yang sudah terverifikasi yang bisa mengaksesnya.
    Route::get('/{slug}/edit', [ExamController::class, 'edit'])->name('cbt.edit');
});


Route::middleware('auth')->group(function () {
    // untuk menampilkan halaman edit profil
    Route::get('/settings', [ProfileController::class, 'edit'])->name('profile.edit');

    // untuk memperbarui data profil khususnya email
    Route::patch('/settings', [ProfileController::class, 'update'])->name('profile.update');

    //untuk menghapus data profil
    Route::delete('/settings', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
