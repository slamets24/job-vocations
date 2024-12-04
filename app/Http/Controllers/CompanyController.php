<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CompanyController extends Controller
{
    // Fungsi untuk menampilkan daftar perusahaan terbaru
    public function index()
    {
        // Mengambil data 15 perusahaan terbaru dari database
        $companies = DB::table('company_profiles')
            ->select('id', 'company_name', 'slug', 'image_profile as image') // Pilih kolom yang diperlukan
            ->limit(15)
            ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal dibuat secara descending
            ->get();

        // Memproses setiap perusahaan untuk memastikan gambar memiliki URL yang valid
        $companies->transform(function ($company) {
            // Jika ada gambar profil, gunakan URL gambar di storage; jika tidak, gunakan gambar placeholder
            $company->image = $company->image ? Storage::url($company->image) : asset('images/placeholder-company.jpg');
            return $company;
        });

        // Render halaman menggunakan Inertia dengan daftar perusahaan yang telah diproses
        return Inertia::render('Company/FindCompany/Index', compact('companies'));
    }

    // Fungsi untuk menampilkan detail perusahaan berdasarkan slug
    public function show(string $slug)
    {
        // Mengambil detail perusahaan dari database, termasuk data lokasi (provinsi, kota, kecamatan)
        $company = DB::table('company_profiles as c')
            ->leftJoin('indonesia_provinces as p', 'c.province_id', '=', 'p.code') // Gabungkan dengan tabel provinsi
            ->leftJoin('indonesia_cities as ct', 'c.city_id', '=', 'ct.code') // Gabungkan dengan tabel kota
            ->leftJoin('indonesia_districts as d', 'c.district_id', '=', 'd.code') // Gabungkan dengan tabel kecamatan
            ->where('c.slug', $slug) // Cari berdasarkan slug
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
            ->first(); // Ambil data pertama yang cocok

        // Pastikan gambar profil memiliki URL yang valid
        $company->image_profile = $company->image_profile ? Storage::url($company->image_profile) : asset('images/placeholder-company.jpg');

        // Render halaman detail perusahaan menggunakan Inertia
        return Inertia::render('Company/Profile/Detail', compact('company'));
    }

    // Fungsi untuk mencari perusahaan berdasarkan nama
    public function search(Request $request)
    {
        // Mendapatkan query pencarian dari input user
        $query = $request->input('search');

        $companies = []; // Inisialisasi array kosong untuk hasil pencarian
        if ($query) {
            // Melakukan pencarian berdasarkan nama perusahaan
            $companies = DB::table('company_profiles')
                ->where('company_profiles.company_name', 'like', '%' . $query . '%') // Pencarian dengan pola wildcard
                ->select(
                    'company_profiles.id',
                    'company_profiles.company_name',
                    'company_profiles.image_profile as image',
                    'company_profiles.slug'
                )
                ->get();

            // Pastikan setiap gambar perusahaan memiliki URL yang valid
            $companies->transform(function ($company) {
                $company->image = $company->image ? Storage::url($company->image) : asset('images/placeholder-company.jpg');
                return $company;
            });
        }

        // Render halaman hasil pencarian menggunakan Inertia
        return Inertia::render('Company/FindCompany/SearchResult', compact('companies', 'query'));
    }
}
