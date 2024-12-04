<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompanyProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->type === 'company';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user_id = Auth::user()->id;
        $company = DB::table('company_profiles')
            ->where('user_id', $user_id)
            ->select('id')
            ->first();

        return [
            'imageProfile' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'companyName' => [
                'required',
                'string',
                'max:80',
                Rule::unique('company_profiles', 'company_name')->ignore($company->id),
            ],
            'companyAddress' => 'required|string|max:255',
            'media' => 'nullable|url',
            'selectedProvince' => 'required|exists:indonesia_provinces,code',
            'selectedCity' => 'required|exists:indonesia_cities,code',
            'selectedDistrict' => 'required|exists:indonesia_districts,code',
            'description' => 'required|string|min:10',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'imageProfile.image' => 'File yang diunggah harus berupa gambar.',
            'imageProfile.mimes' => 'Jenis file yang diperbolehkan: jpeg, png, jpg.',
            'imageProfile.max' => 'Ukuran file maksimum adalah 2MB.',
            'companyName.required' => 'Nama perusahaan wajib diisi.',
            'companyName.max' => 'Nama perusahaan tidak boleh lebih dari 80 karakter.',
            'companyName.unique' => 'Nama perusahaan sudah terdaftar.',
            'companyAddress.required' => 'Alamat perusahaan wajib diisi.',
            'companyAddress.max' => 'Alamat perusahaan tidak boleh lebih dari 255 karakter.',
            'media.url' => 'Sosial media harus berupa URL yang valid.',
            'selectedProvince.required' => 'Provinsi wajib dipilih.',
            'selectedProvince.exists' => 'Provinsi yang dipilih tidak valid.',
            'selectedCity.required' => 'Kota/Kabupaten wajib dipilih.',
            'selectedCity.exists' => 'Kota/Kabupaten yang dipilih tidak valid.',
            'selectedDistrict.required' => 'Kecamatan wajib dipilih.',
            'selectedDistrict.exists' => 'Kecamatan yang dipilih tidak valid.',
            'description.required' => 'Deskripsi perusahaan wajib diisi.',
            'description.min' => 'Deskripsi harus memiliki minimal 10 karakter.',
        ];
    }
}
