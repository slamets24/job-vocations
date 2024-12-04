<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FlowPersonalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Akun Personal
        $user = User::create([
            'email' => 'ezabariq.work@gmail.com',
            'password' => Hash::make('Rahasia123#'),
            'type' => 'personal',
            'email_verified_at' => Carbon::now(),
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Tambahkan entri ke personal_profiles
        DB::table('personal_profiles')->insert([
            'user_id' => $user->id,
            'image_profile' => null,
            'full_name' => 'Eza Bariq',
            'birth_date' => '1990-01-01',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'biography' => 'Seorang developer dengan pengalaman luas.',
            'phone' => '081234567890',
            'slug' => Str::slug('Eza Bariq' . '-' . Str::ulid()),
            'province_id' => 32,
            'city_id' => 3215,
            'district_id' => 321526,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'salary' => 10000000.00,
            'job_classification_id' => 18,
            'job_type' => 'full time',
        ]);
    }
}
