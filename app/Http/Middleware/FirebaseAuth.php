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

                                           // Check if current path is a public route
        $currentPath   = $request->path(); // returns 'about', 'login', etc.
        $isPublicRoute = in_array($currentPath, $publicRoutes);

        // If it's a public route, allow access without authentication
        if ($isPublicRoute) {
            return $next($request);
        }

        // For protected routes (like dashboard), check authentication
        if (! session('firebase_authenticated')) {
            return redirect('/login')->with('error', 'Please login to access this page.');
        }

        // Optional: Verify the user still exists in Firebase
        $user = session('firebase_user');
        if ($user) {
            $firebaseUser = $this->firebaseService->getUser($user['uid']);
            if (! $firebaseUser['success']) {
                // User no longer exists in Firebase
                $request->session()->invalidate();
                return redirect('/login')->with('error', 'Session expired. Please login again.');
            }
        }

        return $next($request);
    }
}
