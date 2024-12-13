<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        if ($user && $user->role === 'HRD') {
            $hrdDetail = $user->hrdDetail;
            if ($hrdDetail && $hrdDetail->profile_image) {
                $filename = basename($hrdDetail->profile_image);
                $hrdDetail->profile_image_url = route('hrd.profile.image', $filename);
            }
            $user->hrd_detail = $hrdDetail;
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
        ]);
    }
}
