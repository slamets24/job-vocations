<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $userId = $user->id;

        $profile = $this->getUserProfile($user->type, $userId);

        $isProfileFilled = $this->isProfileComplete($profile, $user->type);

        if ($isProfileFilled) {
            return $next($request);
        }

        return $user->type === 'company' ? redirect()->route('company.profile.create') : redirect()->route('personal.create');
    }

    /**
     * Dapatkan data profil berdasarkan tipe pengguna.
     */
    private function getUserProfile(string $userType, int $userId)
    {
        if ($userType === 'company') {
            return DB::table('company_profiles')
                ->where('user_id', $userId)
                ->select('image_profile', 'company_name', 'company_address', 'description', 'province_id', 'city_id', 'district_id')
                ->first();
        }

        return DB::table('personal_profiles')
            ->where('user_id', $userId)
            ->select('image_profile', 'full_name', 'address', 'birth_date', 'phone', 'province_id', 'city_id', 'district_id')
            ->first();
    }

    /**
     * Cek apakah data profil sudah lengkap berdasarkan tipe pengguna.
     */
    private function isProfileComplete($profile, string $userType): bool
    {
        if (!$profile) {
            return false;
        }

        $requiredFields = $userType === 'company'
            ? ['image_profile', 'company_name', 'description', 'company_address', 'province_id', 'city_id', 'district_id']
            : ['image_profile', 'full_name', 'address', 'birth_date', 'phone', 'province_id', 'city_id', 'district_id'];


        foreach ($requiredFields as $field) {
            if (empty($profile->{$field})) {
                return false;
            }
        }

        return true;
    }
}
