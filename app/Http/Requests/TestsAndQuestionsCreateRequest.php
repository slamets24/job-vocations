<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestsAndQuestionsCreateRequest extends FormRequest
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
            'job_posting_id' => 'required|exists:job_postings,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string|max:255',
            'questions.*.options' => 'required|array|min:2|max:5',
            'questions.*.options.*.option_text' => 'required|string|max:255',
            'questions.*.options.*.points' => 'required|integer|min:0',
            'questions.*.options.*.is_correct' => 'required|boolean',
            'questions.*.correctOptionIndex' => 'required|integer|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'job_posting_id.required' => 'Pekerjaan harus dipilih.',
            'job_posting_id.exists' => 'Pekerjaan tidak ditemukan.',

            'name.required' => 'Nama soal harus diisi.',
            'name.string' => 'Nama soal harus berupa teks.',
            'name.max' => 'Nama soal tidak boleh lebih dari 255 karakter.',

            'description.string' => 'Deskripsi soal harus berupa teks.',

            'duration.required' => 'Durasi harus diisi.',
            'duration.integer' => 'Durasi harus berupa angka.',
            'duration.min' => 'Durasi tidak boleh kurang dari 1.',

            'passing_score.required' => 'Passing score harus diisi',
            'passing_score.integer' => 'Passing score harus berupa angka',
            'passing_score.min' => 'Passing score tidak boleh kurang dari 0',

            'questions.required' => 'Setidaknya satu pertanyaan harus ditambahkan.',
            'questions.min' => 'Setidaknya satu pertanyaan harus ditambahkan.',

            'questions.*.question.required' => 'Pertanyaan tidak boleh kosong.',

            'questions.*.options.array' => 'Setiap pertanyaan harus memiliki setidaknya dua opsi.',
            'questions.*.options.required' => 'Setiap pertanyaan harus memiliki setidaknya dua opsi.',
            'questions.*.options.min' => 'Setiap pertanyaan harus memiliki minimal dua opsi.',
            'questions.*.options.max' => 'Setiap pertanyaan harus memiliki maksimal lima opsi.',

            'questions.*.options.*.option_text.required' => 'Teks opsi tidak boleh kosong.',

            'questions.*.options.*.points.required' => 'Poin harus diisi.',
            'questions.*.options.*.points.integer' => 'Poin harus berupa angka.',
            'questions.*.options.*.points.min' => 'Poin tidak boleh kurang dari 0.',

            'questions.*.options.*.is_correct.required' => 'Jawaban benar harus ditentukan.',
            'questions.*.options.*.is_correct.boolean' => 'Jawaban benar harus bernilai true atau false.',

            'questions.*.correctOptionIndex.required' => 'Jawaban benar harus ditentukan.',
            'questions.*.correctOptionIndex.integer' => 'Jawaban benar harus berupa angka.',
            'questions.*.correctOptionIndex.min' => 'Jawaban benar tidak boleh kurang dari 0.',
        ];
    }
}
