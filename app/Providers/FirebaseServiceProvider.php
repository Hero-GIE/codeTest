<?php
namespace App\Providers;

use App\Services\FirebaseService;
use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });

        // Register the Firebase database instance
        $this->app->singleton('firebase.database', function ($app) {
            $firebaseService = $app->make(FirebaseService::class);
            return $firebaseService->getDatabase();
        });

        // Register the Firebase auth instance
        $this->app->singleton('firebase.auth', function ($app) {
            $firebaseService = $app->make(FirebaseService::class);
            return $firebaseService->getAuth();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
