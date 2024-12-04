<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Password::defaults()],
            'type' => 'required|string|in:personal,company',
            'is_aggreed' => 'required|accepted'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.lowercase' => 'Email harus berupa huruf kecil.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan.',

            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Password tidak cocok.',
            'password' => 'Password harus setidaknya 8 karakter dan mengandung huruf besar, huruf kecil, angka, dan karakter khusus.',

            'type.required' => 'Jenis pengguna wajib diisi.',
            'type.string' => 'Jenis pengguna harus berupa teks.',
            'type.in' => 'Jenis pengguna harus personal atau company.',

            'is_aggreed.required' => 'Anda harus menyetujui syarat dan ketentuan.',
            'is_aggreed.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ];
    }
}
