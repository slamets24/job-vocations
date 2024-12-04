<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->hasAny(['search', 'classification', 'location'])) {
            $jobs = $this->filterJobs($request);
            return inertia('Personal/Jobs/Search', compact('jobs'));
        }

        $recommendedJobs = null;

        // Jika sudah login, maka ambil data yang sedang login
        if ($user && $user->type === 'personal') {
            $personalProfile = DB::table('personal_profiles')
                ->select('id', 'job_classification_id',
                    'salary',
                    'job_type',
                    'city_id',
                    'province_id',
                )
                ->where('user_id', $user->id)
                ->first();

            if (!$personalProfile) {
                $recommendedJobs = null;
            } else {
                $recommendedJobs = $this->getJobRecommendations($personalProfile);
            }
        }


        $jobOthers = $this->getJobsLimit();

        return inertia('Personal/Jobs/Search', compact('recommendedJobs', 'jobOthers'));
    }

    private function getJobRecommendations($personalProfile): \Illuminate\Support\Collection
    {
        $jobRecommendations = DB::table('job_postings')
            ->join('job_classifications', 'job_postings.job_classification_id', '=', 'job_classifications.id')
            ->join('indonesia_cities', 'job_postings.city_id', '=', 'indonesia_cities.code')
            ->join('indonesia_provinces', 'job_postings.province_id', '=', 'indonesia_provinces.code')
            ->join('users', 'job_postings.user_id', '=', 'users.id')
            ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
            ->where('job_postings.status', 1)

            // start recommendation
            ->where(function ($query) use ($personalProfile) {
                $query->where('job_postings.job_classification_id', $personalProfile->job_classification_id)
                    ->whereBetween('job_postings.salary', [
                        $personalProfile->salary - 1000000,
                        $personalProfile->salary + 1000000,
                    ])
                    ->where(function ($query) use ($personalProfile) {
                        $query->where('job_postings.job_type', $personalProfile->job_type)
                            ->orWhere('job_postings.city_id', $personalProfile->city_id)
                            ->orWhere('job_postings.province_id', $personalProfile->province_id);
                    });
            })
            ->select(
                'job_postings.id',
                'job_postings.title',
                'job_postings.salary',
                'job_postings.slug',
                'job_postings.created_at',
                'job_postings.status',
                'job_classifications.name as job_classification',
                'indonesia_cities.name as city_name',
                'indonesia_provinces.name as province_name',
                'company_profiles.company_name as company_name',
                'company_profiles.image_profile as image'
            )
            ->orderBy('job_postings.created_at', 'desc')
            ->limit(10)
            ->get();

        $jobRecommendations->transform(function ($job) {
            $job->created_at = Carbon::parse($job->created_at)->diffForHumans();
            $job->image = $job->image ? Storage::url($job->image) : asset('images/placeholder-company.jpg');
            return $job;
        });

        return $jobRecommendations;
    }

    private function getJobsLimit()
    {
        // Tentukan limit berdasarkan status login
        $limit = Auth::check() ? 15 : 4;

        $jobOthers = DB::table('job_postings')
            ->join('indonesia_cities', 'job_postings.city_id', '=', 'indonesia_cities.code')
            ->join('indonesia_provinces', 'job_postings.province_id', '=', 'indonesia_provinces.code')
            ->join('users', 'job_postings.user_id', '=', 'users.id')
            ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
            ->where('job_postings.status', 1)
            ->select(
                'job_postings.id',
                'job_postings.title',
                'job_postings.salary',
                'job_postings.slug',
                'job_postings.created_at',
                'job_postings.status',
                'indonesia_cities.name as city_name',
                'indonesia_provinces.name as province_name',
                'company_profiles.company_name as company_name',
                'company_profiles.image_profile as image'
            )
            ->orderBy('job_postings.created_at', 'desc')
            ->limit($limit)
            ->get();

        $jobOthers->transform(function ($job) {
            $job->created_at = Carbon::parse($job->created_at)->diffForHumans();
            $job->image = $job->image ? Storage::url($job->image) : asset('images/placeholder-company.jpg');
            return $job;
        });

        return $jobOthers;
    }

    private function filterJobs(Request $request)
    {
        $query = DB::table('job_postings')
            ->join('indonesia_provinces', 'job_postings.province_id', '=', 'indonesia_provinces.code')
            ->join('indonesia_cities', 'job_postings.city_id', '=', 'indonesia_cities.code')
            ->join('users', 'job_postings.user_id', '=', 'users.id')
            ->join('company_profiles', 'company_profiles.user_id', '=', 'users.id')
            ->select(
                'job_postings.id',
                'job_postings.title',
                'company_profiles.company_name as company_name',
                'indonesia_provinces.name as province_name',
                'indonesia_cities.name as city_name',
                'job_postings.slug',
                'job_postings.salary',
                'job_postings.status',
                'job_postings.created_at',
                'company_profiles.image_profile as image'
            )
            ->where('job_postings.status', 1);

        // Filter by keyword (title)
        $query->when($request->input('search'), function ($q, $search) {
            $q->where('job_postings.title', 'like', '%' . $search . '%');
        });

        // Filter by classification
        $query->when($request->input('classification'), function ($q, $classification) {
            $q->where('job_postings.job_classification_id', $classification);
        });

        // Filter by city or province
        $query->when($request->input('location'), function ($q, $location) {
            // Pecahkan input menjadi city_code dan province_code
            [$cityCode, $provinceCode] = array_pad(explode(',', $location), 2, null);

            $q->where(function ($subQuery) use ($cityCode, $provinceCode) {
                if ($cityCode) {
                    // Jika city_code tersedia, hanya filter berdasarkan city_id
                    $subQuery->where('job_postings.city_id', $cityCode);
                } else if ($provinceCode) {
                    // Jika city_code tidak tersedia, filter berdasarkan province_id
                    $subQuery->where('job_postings.province_id', $provinceCode);
                }
            });
        });


        $query->orderBy('job_postings.created_at', 'desc');

        $jobs = $query->paginate(10)
            ->appends($request->query())
            ->through(function ($job) {
                $job->created_at = Carbon::parse($job->created_at)->diffForHumans();
                $job->image = $job->image ? Storage::url($job->image) : asset('images/placeholder-company.jpg');
                return $job;
            });

        return $jobs;
    }
}
