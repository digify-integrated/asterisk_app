<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use App\Services\NavigationMenuBuilder;

class ShareNavigationData
{
    public function __construct(
        protected NavigationMenuBuilder $menuBuilder
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $appId = (int) $request->route('appId', 0);

        if ($user) {
            // Manage profile picture URL formatting neatly
            $defaultProfilePicture = asset('assets/media/default/default-avatar.jpg');
            $path = trim((string) ($user->profile_picture ?? ''));

            $profilePictureUrl = ($path !== '' && Storage::disk('public')->exists($path))
                ? Storage::url($path)
                : $defaultProfilePicture;

            // Generate menu sidebar tree structure if within an app context
            $navTree = [];
            if ($appId > 0) {
                $navTree = $this->menuBuilder->buildForUserAndApp($user->id, $appId);
            }

            // Share globally across any layouts rendered on this lifecycle loop
            View::share([
                'nav_app_id'         => $appId,
                'nav_tree'           => $navTree,
                'navName'            => $user->name,
                'navEmail'           => $user->email,
                'navProfilePicture'  => $profilePictureUrl,
            ]);
        }

        return $next($request);
    }
}
