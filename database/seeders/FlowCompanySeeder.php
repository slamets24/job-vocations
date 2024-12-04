<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FlowCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Akun Company
        $user = User::create([
            'email' => 'bersamamaju.company@gmail.com',
            'password' => Hash::make('Rahasia123#'),
            'type' => 'company',
            'email_verified_at' => Carbon::now(),
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Company Profile
        $sourcePath = public_path('images/placeholder-company.jpg');

        // Path tujuan di storage/app/public/company-profiles
        $destinationPath = 'company-profiles/placeholder-company.jpg';

        // Pastikan file ada di public/images sebelum menyalin
        if (file_exists($sourcePath)) {
            // Salin file ke storage disk 'public'
            Storage::disk('public')->put($destinationPath, file_get_contents($sourcePath));
        }

        DB::table('company_profiles')->insert([
            'user_id' => $user->id,
            'image_profile' => $destinationPath,
            'company_name' => 'PT. Bersama Maju Terus',
            'company_address' => 'Jl. Contoh Alamat ' . rand(1, 100),
            'description' => 'Career Opportunities Bank DBS Indonesia is part of DBS Group, one of the largest financial services groups in Asia with dominant positions in consumer banking, treasury and markets, asset management, securities brokerage, equity and debt fund raising. With expansion of the Bank, we are looking for high caliber individuals to fill in the following position',
            'social_media' => 'https://www.linkedin.com/company/huawei-digitalpower/',
            'slug' => Str::slug('PT. Bersama Maju Terus'),
            'province_id' => 32,
            'city_id' => 3215,
            'district_id' => 321526,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Membuat Lowongan
        DB::table('job_postings')->insert([
            [
                'user_id' => $user->id,
                'title' => 'Software Engineer',
                'description' => 'We are looking for a skilled software engineer to join our team.',
                'salary' => 4000000,
                'education' => 's1',
                'slug' => Str::slug('Software Engineer') . '-' . Str::ulid(),
                'job_type' => 'full time',
                'province_id' => 32,
                'city_id' => 3215,
                'district_id' => 321526,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status' => 1, // Status aktif
                'job_classification_id' => 18,
            ],
            [
                'user_id' => $user->id,
                'title' => 'Graphic Designer',
                'description' => 'Join our creative team as a graphic designer.',
                'salary' => 3000000,
                'education' => 'slta/sma/smk',
                'slug' => Str::slug('Graphic Designer') . '-' . Str::ulid(),
                'job_type' => 'contract',
                'province_id' => 32,
                'city_id' => 3215,
                'district_id' => 321526,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'status' => 1,
                'job_classification_id' => 18,
            ],
        ]);
    }
}
