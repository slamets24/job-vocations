<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomicileController extends Controller
{
    /**
     * Mendapatkan daftar semua provinsi dari tabel `indonesia_provinces`.
     *
     * @return \Illuminate\Http\JsonResponse Daftar provinsi dalam format JSON.
     */
    public function getProvinces()
    {
        // Query untuk mendapatkan semua data provinsi
        $provinces = DB::table('indonesia_provinces')->get();

        // Mengembalikan data provinsi sebagai respons JSON
        return response()->json($provinces);
    }

    /**
     * Mendapatkan daftar semua kota berdasarkan ID provinsi tertentu.
     *
     * @param string $provinceId ID provinsi (kode provinsi) yang digunakan sebagai filter.
     * @return \Illuminate\Http\JsonResponse Daftar kota dalam format JSON.
     */
    public function getCities($provinceId)
    {
        // Query untuk mendapatkan semua data kota berdasarkan kode provinsi
        $cities = DB::table('indonesia_cities')
            ->where('province_code', $provinceId) // Filter berdasarkan kode provinsi
            ->get();

        // Mengembalikan data kota sebagai respons JSON
        return response()->json($cities);
    }

    /**
     * Mendapatkan daftar semua kecamatan berdasarkan ID kota tertentu.
     *
     * @param string $cityId ID kota (kode kota) yang digunakan sebagai filter.
     * @return \Illuminate\Http\JsonResponse Daftar kecamatan dalam format JSON.
     */
    public function getDistricts($cityId)
    {
        // Query untuk mendapatkan semua data kecamatan berdasarkan kode kota
        $districts = DB::table('indonesia_districts')
            ->where('city_code', $cityId) // Filter berdasarkan kode kota
            ->get();

        // Mengembalikan data kecamatan sebagai respons JSON
        return response()->json($districts);
    }

    /**
     * Mencari kota berdasarkan nama kota (menggunakan pencarian like).
     *
     * @param \Illuminate\Http\Request $request Permintaan yang berisi input nama kota.
     * @return \Illuminate\Http\JsonResponse Daftar kota beserta provinsi terkait dalam format JSON.
     */
    public function getCitiesByName(Request $request)
    {
        // Mendapatkan input nama kota dari permintaan pengguna
        $cityName = $request->input('cityName');

        // Query untuk mendapatkan kota dan provinsi yang sesuai dengan nama kota yang dicari
        $provinces = DB::table('indonesia_provinces')
            ->join('indonesia_cities', 'indonesia_provinces.code', '=', 'indonesia_cities.province_code') // Bergabung dengan tabel `indonesia_cities`
            ->select(
                'indonesia_provinces.code as province_code', // Kolom kode provinsi
                'indonesia_provinces.name as province_name', // Kolom nama provinsi
                'indonesia_cities.code as city_code', // Kolom kode kota
                'indonesia_cities.name as city_name' // Kolom nama kota
            )
            ->where('indonesia_cities.name', 'like', '%' . $cityName . '%') // Filter nama kota menggunakan operator LIKE
            ->get();

        // Mengembalikan daftar kota dan provinsi terkait sebagai respons JSON
        return response()->json($provinces);
    }
}
