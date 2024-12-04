<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckJobApplication
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = Auth::id();
        $slug = $request->route('slug');

        $job = DB::table('job_postings')
            ->where('slug', $slug)
            ->first();

        // Cek apakah user telah melamar pada job posting tersebut
        $hasApplied = DB::table('applications')->where('user_id', $userId)
            ->where('job_posting_id', $job->id)
            ->exists();

        if ($hasApplied) {
            return abort(403, 'Anda telah melamar pada job posting ini.');
        }

        return $next($request);
    }
}
