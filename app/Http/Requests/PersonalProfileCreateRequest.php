<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalProfileCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->type === 'personal';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'imageProfile' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'full_name' => 'required|string|max:100',
            'birthDate' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'biography' => 'required|string',
            'selectedProvince' => 'required|exists:indonesia_provinces,code',
            'selectedCity' => 'required|exists:indonesia_cities,code',
            'selectedDistrict' => 'required|exists:indonesia_districts,code',
            'job_classification' => 'required|numeric',
            'salary' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'imageProfile.required' => 'Gambar profil wajib diunggah.',
            'imageProfile.image' => 'File yang diunggah harus berupa gambar.',
            'imageProfile.mimes' => 'Jenis file yang diperbolehkan: jpeg, png, jpg.',
            'imageProfile.max' => 'Ukuran file maksimum adalah 2MB.',

            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.string' => 'Nama lengkap harus berupa teks.',
            'full_name.max' => 'Panjang nama lengkap maksimum adalah 100 karakter.',

            'birthDate.required' => 'Tanggal lahir wajib diisi.',
            'birthDate.date' => 'Format tanggal lahir harus berupa "YYYY-MM-DD".',

            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.string' => 'Nomor telepon harus berupa teks.',
            'phone.max' => 'Panjang nomor telepon maksimum adalah 20 karakter.',

            'address.required' => 'Alamat wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',

            'biography.required' => 'Biografi wajib diisi.',
            'biography.string' => 'Biografi harus berupa teks.',

            'selectedProvince.required' => 'Provinsi wajib dipilih.',
            'selectedProvince.exists' => 'Provinsi yang dipilih tidak valid.',
            'selectedCity.required' => 'Kota/Kabupaten wajib dipilih.',
            'selectedCity.exists' => 'Kota/Kabupaten yang dipilih tidak valid.',
            'selectedDistrict.required' => 'Kecamatan wajib dipilih.',
            'selectedDistrict.exists' => 'Kecamatan yang dipilih tidak valid.',

            'job_classification.required' => 'Klasifikasi pekerjaan wajib diisi.',
            'job_classification.numeric' => 'Klasifikasi pekerjaan harus berupa angka.',

            'salary.required' => 'Gaji wajib diisi.',
            'salary.numeric' => 'Gaji harus berupa angka.',
        ];
    }
}
