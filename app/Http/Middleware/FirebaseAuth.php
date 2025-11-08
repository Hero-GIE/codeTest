<?php
namespace App\Http\Middleware;

use App\Services\FirebaseService;
use Closure;
use Illuminate\Http\Request;

class FirebaseAuth
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Define public routes that don't require authentication
        $publicRoutes = [
            '', // root
            'home',
            'about',
            'gallery',
            'contact',
            'login',
            'register',
        ];

        $currentPath   = $request->path();
        $isPublicRoute = in_array($currentPath, $publicRoutes);

        // If it's a public route, allow access without authentication
        if ($isPublicRoute) {
            return $next($request);
        }

        // For protected routes (like dashboard), check authentication
        if (! session('firebase_authenticated')) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        // ✅ ADDED: Verify user exists in our database, not just Firebase Auth
        $user = session('firebase_user');
        if ($user) {
            $userProfile = $this->firebaseService->getUserProfile($user['uid']);

            if (empty($userProfile['data'])) {
                // User authenticated but not in our database - invalid session
                $request->session()->invalidate();
                return redirect('/login')->with('error', 'Account not found. Please register first.');
            }
        }

        return $next($request);
    }
}
