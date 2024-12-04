<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestsAndQuestionsCreateRequest;
use App\Mail\PassedTypeStatusMail;
use App\Mail\RejectionNotificationMail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($slug)
    {
        // Ambil data pekerjaan berdasarkan slug
        $job = DB::table('job_postings')->where('slug', $slug)->firstOrFail();

        // Cek apakah soal sudah dibuat untuk job ini
        $test = DB::table('tests')
            ->where('job_posting_id', $job->id)
            ->first();

        if ($test) {
            // Redirect ke halaman edit jika soal sudah ada
            return redirect()->route('cbt.edit', $job->slug)
                ->with('info', 'Soal sudah dibuat untuk pekerjaan ini. Anda dapat mengedit soal.');
        }

        // Render halaman create jika belum ada soal
        return Inertia::render('Company/CBT/Create', compact('job'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TestsAndQuestionsCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            // Masukan data test
            $testId = DB::table('tests')->insertGetId([
                'job_posting_id' => $request->job_posting_id,
                'name' => trim($request->name),
                'description' => $request->description ? trim($request->description) : null,
                'duration' => (int)$request->duration,
                'passing_score' => (int)$request->passing_score,
                'slug' => Str::slug($request->name) . '-' . Str::ulid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // proses pertanyaan dan pilihan
            foreach ($request->questions as $index => $questionData) {
                // validasi jumlah jawaban yang benar
                $correctCount = collect($questionData['options'])->where('is_correct', true)->count();
                if ($correctCount !== 1) {
                    throw ValidationException::withMessages([
                        "questions.$index.correct_answer" => 'Setiap pertanyaan harus memiliki tepat satu jawaban yang benar'
                    ]);
                }

                // tambah pertanyaan
                $questionId = DB::table('questions')->insertGetId([
                    'test_id' => $testId,
                    'question' => trim($questionData['question']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // tambah pilihan
                $optionsToInsert = array_map(function ($option) use ($questionId) {
                    return [
                        'question_id' => $questionId,
                        'option_text' => trim($option['option_text']),
                        'points' => (int)$option['points'],
                        'is_correct' => (bool)$option['is_correct'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $questionData['options']);

                DB::table('options')->insert($optionsToInsert);
            }

            DB::commit();
            Log::notice("Test dengan ID $testId berhasil dibuat!");
            return redirect()->route('jobs.index')->with('success', 'Test berhasil dibuat!');
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("Database Error: " . $e->getMessage());
            return back()->withErrors(['dbError' => 'Terjadi kesalahan pada sistem, silakan coba lagi nanti!'])->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::critical("System Error: " . $e->getMessage());
            return back()->withErrors(['generalError' => 'Terjadi kesalahan, silakan coba lagi nanti!'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        // Ambil data test beserta job_posting melalui join
        $test = DB::table('tests')
            ->join('job_postings', 'tests.job_posting_id', '=', 'job_postings.id')
            ->where('job_postings.slug', $slug)
            ->select('tests.*', 'job_postings.slug as job_slug') // Pilih kolom yang dibutuhkan
            ->first();

        if (!$test) {
            return redirect()->route('applications.proposed')->with(['error' => 'Tes tidak ditemukan untuk job posting ini.']);
        }

        // Validasi status aplikasi
        $application = DB::table('applications')
            ->where('user_id', auth::id()) // ID pengguna yang login
            ->where('job_posting_id', $test->job_posting_id) // Validasi berdasarkan job_posting_id
            ->where('status', 'test') // Status harus "test"
            ->first();

        if (!$application) {
            return abort(403, 'Anda telah melakukan ini sebelumnya.');
        }

        // Ambil pertanyaan terkait test
        $questions = DB::table('questions')
            ->where('test_id', $test->id)
            ->get();

        // Ambil opsi terkait dengan masing-masing pertanyaan
        $options = DB::table('options')
            ->whereIn('question_id', $questions->pluck('id'))
            ->get();

        // Susun data pertanyaan dengan opsi terkait
        $questionsWithOptions = $questions->map(function ($question) use ($options) {
            $question->options = $options->where('question_id', $question->id)->values();
            return $question;
        });
        // dd($questionsWithOptions);
        return Inertia::render('Personal/Users/CBT/Cbt', [
            'test' => $test,
            'questions' => $questionsWithOptions,
            'timeLimit' => $test->duration
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug)
    {
        // Cari job berdasarkan slug
        $job = DB::table('job_postings')->where('slug', $slug)->first();

        if (!$job) {
            return redirect()->back()->with('error', 'Pekerjaan tidak ditemukan.');
        }

        // Ambil data soal berdasarkan job_id
        $test = DB::table('tests')
            ->where('job_posting_id', $job->id)
            ->first();

        if (!$test) {
            return redirect()->back()->with('error', 'Soal tidak ditemukan.');
        }

        // Ambil pertanyaan dan opsi terkait menggunakan join
        $questionsData = DB::table('questions')
            ->where('questions.test_id', $test->id)
            ->leftJoin('options', 'questions.id', '=', 'options.question_id')
            ->select(
                'questions.id as question_id',
                'questions.question',
                'options.id as option_id',
                'options.option_text',
                'options.is_correct',
                'options.points'
            )
            ->get();

        // Grupkan data pertanyaan dan opsi
        $questions = $questionsData->groupBy('question_id')->map(function ($group) {
            $question = $group->first();
            $correctIndex = $group->pluck('is_correct')->search(1); // Cari index dari opsi yang benar
            return [
                'id' => $question->question_id,
                'question' => $question->question,
                'options' => $group->map(function ($option) {
                    return [
                        'id' => $option->option_id,
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'points' => $option->points,
                    ];
                }),
                'correctOptionIndex' => $correctIndex, // Tambahkan index jawaban benar
            ];
        })->values();


        return inertia('Company/CBT/Edit', [
            'test' => $test,
            'questions' => $questions,
            'job' => $job,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Log data request untuk debugging
            Log::info('Update Request Data: ', $request->all());

            // Validasi data request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'required|integer|min:1',
                'passing_score' => 'required|integer|min:0',
                'questions' => 'required|array|min:1',
                'questions.*.question' => 'required|string',
                'questions.*.options' => 'required|array|min:2|max:5',
                'questions.*.options.*.option_text' => 'required|string',
                'questions.*.options.*.points' => 'required|integer|min:0',
                'questions.*.correctOptionIndex' => 'required|integer|min:0',
            ]);

            // Log data validasi yang berhasil
            Log::info('Validated Data: ', $validated);

            // Mulai transaksi database
            DB::beginTransaction();

            // Update data test utama
            DB::table('tests')->where('id', $id)->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'duration' => $validated['duration'],
                'passing_score' => $validated['passing_score'],
                'updated_at' => now(),
            ]);

            // Simpan ID pertanyaan yang masih ada
            $existingQuestionIds = [];

            foreach ($validated['questions'] as $question) {
                if (isset($question['id'])) {
                    // Update pertanyaan yang ada
                    DB::table('questions')->where('id', $question['id'])->update([
                        'question' => $question['question'],
                        'updated_at' => now(),
                    ]);

                    $existingQuestionIds[] = $question['id'];

                    // Simpan opsi pertanyaan
                    $existingOptionIds = [];
                    foreach ($question['options'] as $index => $option) {
                        if (isset($option['id'])) {
                            // Update opsi yang ada
                            DB::table('options')->where('id', $option['id'])->update([
                                'option_text' => $option['option_text'],
                                'is_correct' => $index === $question['correctOptionIndex'],
                                'points' => $option['points'],
                                'updated_at' => now(),
                            ]);
                            $existingOptionIds[] = $option['id'];
                        } else {
                            // Buat opsi baru jika belum ada
                            $newOptionId = DB::table('options')->insertGetId([
                                'question_id' => $question['id'],
                                'option_text' => $option['option_text'],
                                'is_correct' => $index === $question['correctOptionIndex'],
                                'points' => $option['points'],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            $existingOptionIds[] = $newOptionId;
                        }
                    }

                    // Hapus opsi yang tidak ada di request
                    DB::table('options')
                        ->where('question_id', $question['id'])
                        ->whereNotIn('id', $existingOptionIds)
                        ->delete();
                } else {
                    // Buat pertanyaan baru jika belum ada
                    $newQuestionId = DB::table('questions')->insertGetId([
                        'test_id' => $id,
                        'question' => $question['question'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Buat opsi untuk pertanyaan baru
                    foreach ($question['options'] as $index => $option) {
                        DB::table('options')->insert([
                            'question_id' => $newQuestionId,
                            'option_text' => $option['option_text'],
                            'is_correct' => $index === $question['correctOptionIndex'],
                            'points' => $option['points'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $existingQuestionIds[] = $newQuestionId;
                }
            }

            // Hapus pertanyaan yang tidak ada di request
            DB::table('questions')
                ->where('test_id', $id)
                ->whereNotIn('id', $existingQuestionIds)
                ->delete();

            // Commit transaksi jika semua berhasil
            DB::commit();

            return redirect()->route('jobs.index')->with('success', 'Test berhasil diubah!');
        } catch (\Exception $e) {
            // Rollback transaksi jika ada kesalahan
            DB::rollBack();

            // Log error untuk debugging
            Log::error('Error Updating Test: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return back()->withErrors(['message' => 'Terjadi kesalahan saat mengedit test: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::table('tests')->where('id', $id)->delete();
            return redirect()->route('jobs.index')->with('success', 'Test berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Terjadi kesalahan saat menghapus test: ' . $e->getMessage()]);
        }
    }

    public function submitAnswers(Request $request)
    {
        $validated = $request->validate([
            'answers' => 'required|array|min:1',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.option_id' => 'required|integer|exists:options,id',
            'test_id' => 'required|integer|exists:tests,id',
        ]);

        $userId = Auth::id();
        $testId = $validated['test_id'];

        // Periksa apakah user sudah memiliki skor untuk test ini
        $scoreExists = DB::table('scores')
            ->where('user_id', $userId)
            ->where('test_id', $testId)
            ->exists();

        if ($scoreExists) {
            return abort(403, 'Anda telah melakukan ini sebelumnya.');
        }

        try {
            DB::beginTransaction();

            // Simpan setiap jawaban
            foreach ($validated['answers'] as $answer) {
                DB::table('user_answers')->insert([
                    'user_id' => $userId,
                    'test_id' => $testId,
                    'question_id' => $answer['question_id'],
                    'option_id' => $answer['option_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Hitung skor berdasarkan poin dari opsi yang dipilih
            $totalScore = DB::table('user_answers')
                ->join('options', 'user_answers.option_id', '=', 'options.id')
                ->where('user_answers.user_id', $userId)
                ->where('user_answers.test_id', $testId)
                ->sum('options.points');

            // Ambil passing score dari tabel tests
            $test = DB::table('tests')
                ->where('id', $testId)
                ->first();

            $passingScore = $test->passing_score;
            $jobPostingId = $test->job_posting_id;

            // Simpan skor ke dalam tabel scores
            DB::table('scores')->insert([
                'user_id' => $userId,
                'test_id' => $testId,
                'score' => $totalScore,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Perbarui status pada tabel applications berdasarkan skor
            $status = $totalScore >= $passingScore ? 'test_passed' : 'test_failed';

            // Update aplikasi dan ambil data dalam satu transaksi
            $data = DB::transaction(function () use ($userId, $jobPostingId, $status) {
                // Update aplikasi
                DB::table('applications')
                    ->where('user_id', $userId)
                    ->where('job_posting_id', $jobPostingId)
                    ->update([
                        'status' => $status,
                        'updated_at' => now(),
                    ]);

                // Ambil data terkait
                return DB::table('applications')
                    ->join('users', 'applications.user_id', '=', 'users.id')
                    ->join('personal_profiles', 'users.id', '=', 'personal_profiles.user_id')
                    ->join('job_postings', 'applications.job_posting_id', '=', 'job_postings.id')
                    ->join('company_profiles', 'job_postings.user_id', '=', 'company_profiles.user_id')
                    ->where('applications.user_id', $userId)
                    ->where('applications.job_posting_id', $jobPostingId)
                    ->select(
                        'job_postings.id as job_posting_id',
                        'personal_profiles.full_name',
                        'job_postings.title',
                        'job_postings.slug',
                        'company_profiles.company_name',
                        'users.email'
                    )
                    ->first();
            });

            // mengirim email tergantun statusnya apa
            if ($status === 'test_passed') {
                $this->passedStatus($data);
            } elseif ($status === 'test_failed') {
                $this->rejectionStatus($data);
            }

            DB::commit();
            return redirect()->route('applications.proposed')->with('success', 'Jawaban berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan jawaban.');
        }
    }

    private function passedStatus($data): void
    {
        // kirim email ke pelamar
        $applicantData = [
            'subject' => "Selamat, Anda Lulus!",
            'user' => (object)[
                'full_name' => $data->full_name,
                'title' => $data->title,
                'company_name' => $data->company_name,
            ],
        ];

        //Kirim email pemberitahuan
        Mail::to($data->email)->send(new PassedTypeStatusMail($applicantData));
    }

    private function rejectionStatus($data): void
    {
        // kirim email ke pelamar
        $applicantData = [
            'subject' => "Tetap Semangat! Terima Kasih atas Lamaran Anda",
            'user' => (object)[
                'full_name' => $data->full_name,
                'title' => $data->title,
                'company_name' => $data->company_name,
            ],
        ];

        //Kirim email pemberitahuan
        Mail::to($data->email)->send(new RejectionNotificationMail($applicantData));
    }
}
