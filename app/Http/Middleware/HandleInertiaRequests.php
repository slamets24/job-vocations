<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $defaultImageUrl = asset('images/placeholder-person.jpg');
        $imageProfile = null;

        if ($user && $user->type === 'company') {
            $imageProfile = DB::table('company_profiles')
                ->where('user_id', $user->id)
                ->value('image_profile');
        } elseif ($user && $user->type === 'personal') {
            $imageProfile = DB::table('personal_profiles')
                ->where('user_id', $user->id)
                ->value('image_profile');
        }

        $imageUrl = $imageProfile ? Storage::url($imageProfile) : $defaultImageUrl;

        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'profile_image' => $imageUrl,
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
