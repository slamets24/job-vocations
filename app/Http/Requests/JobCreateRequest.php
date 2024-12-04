<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobCreateRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:50',
            'description' => 'required|string',
            'salary' => 'required|numeric',
            'education' => 'required|in:sd,smp,slta/sma/smk,d3,d4,s1,s2,s3',
            'job_type' => 'required|in:full time,part time,contract',
            'is_same_location' => 'nullable|boolean',
            'selectedProvince' => 'required_if:is_same_location,false',
            'selectedCity' => 'required_if:is_same_location,false',
            'selectedDistrict' => 'required_if:is_same_location,false',
            'job_classification' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul lowongan wajib diisi.',
            'title.max' => 'Judul lowongan maksimal 50 karakter.',
            'title.string' => 'Judul lowongan harus berupa teks.',
            'description.required' => 'Deskripsi lowongan wajib diisi.',
            'description.string' => 'Deskripsi lowongan harus berupa teks.',
            'salary.required' => 'Gaji wajib diisi.',
            'salary.numeric' => 'Gaji harus berupa angka.',
            'education.required' => 'Pendidikan wajib diisi.',
            'education.in' => 'Pendidikan harus sesuai dengan opsi yang tersedia.',
            'job_type.required' => 'Jenis pekerjaan wajib diisi.',
            'job_type.in' => 'Jenis pekerjaan harus sesuai dengan opsi yang tersedia.',
            'selectedProvince.required_if' => 'Provinsi wajib diisi.',
            'selectedCity.required_if' => 'Kota wajib diisi.',
            'selectedDistrict.required_if' => 'Kecamatan wajib diisi.',
            'job_classification.required' => 'Klasifikasi pekerjaan wajib diisi.',
            'job_classification.numeric' => 'Klasifikasi pekerjaan harus berupa angka.',
        ];
    }

}
