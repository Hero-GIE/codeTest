<?php
namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebsiteController extends Controller
{
    protected $firebaseService;
    protected $analyticsService;

    public function __construct(FirebaseService $firebaseService, AnalyticsService $analyticsService)
    {
        $this->firebaseService  = $firebaseService;
        $this->analyticsService = $analyticsService;
    }

    /**
     * Show public website pages - SIMPLIFIED for localhost
     */
    // public function showPage(Request $request, $page = 'home')
    // {
    //     // Get current user if logged in
    //     $user = session('firebase_user');

    //     // 🔥 CORRECT: Determine whose website we're viewing
    //     $websiteOwnerUid = $this->getWebsiteOwnerUid($request, $user);

    //     // 🔥 RECORD ANALYTICS for the website owner
    //     if ($websiteOwnerUid) {
    //         $this->analyticsService->recordVisit($websiteOwnerUid, $page, $request);
    //     }

    //     // Get content and settings for the website owner
    //     $pageContent     = $this->getPageContentForOwner($websiteOwnerUid, $page);
    //     $websiteSettings = $this->getWebsiteSettingsForOwner($websiteOwnerUid);

    //     // Personalize content ONLY if current user is the website owner
    //     $isOwner = $user && $user['uid'] === $websiteOwnerUid;
    //     if ($isOwner) {
    //         $pageContent = $this->personalizeContentForOwner($user, $page, $pageContent);
    //     }

    //     return Inertia::render('Website/Page', [
    //         'user'            => $user,
    //         'page'            => $page,
    //         'pageContent'     => $pageContent,
    //         'websiteSettings' => $websiteSettings,
    //         'isEditMode'      => $request->has('edit') && $isOwner,
    //         'isOwner'         => $isOwner,
    //     ]);
    // }

    public function showPage(Request $request, $page = 'home')
    {
        // Get current user if logged in
        $user = session('firebase_user');

        // 🔥 CORRECT: Determine whose website we're viewing
        $websiteOwnerUid = $this->getWebsiteOwnerUid($request, $user);

        // Get content and settings for the website owner
        $pageContent     = $this->getPageContentForOwner($websiteOwnerUid, $page);
        $websiteSettings = $this->getWebsiteSettingsForOwner($websiteOwnerUid);

        // Determine if current user is the owner
        $isOwner = $user && $user['uid'] === $websiteOwnerUid;

        // 🔥 CHECK IF PAGE IS PUBLISHED (unless user is the owner)
        if (! $isOwner && ! ($pageContent['published'] ?? false)) {
            // Page is not published and user is not the owner
            abort(404, 'Page not found');
        }

        // 🔥 RECORD ANALYTICS for the website owner (only for published pages)
        if ($websiteOwnerUid && ($pageContent['published'] ?? false)) {
            $this->analyticsService->recordVisit($websiteOwnerUid, $page, $request);
        }

        // Personalize content ONLY if current user is the website owner
        if ($isOwner) {
            $pageContent = $this->personalizeContentForOwner($user, $page, $pageContent);
        }

        // Get list of published pages
        $publishedPages = $this->getPublishedPages($websiteOwnerUid);

        return Inertia::render('Website/Page', [
            'user'            => $user,
            'page'            => $page,
            'pageContent'     => $pageContent,
            'websiteSettings' => $websiteSettings,
            'isEditMode'      => $request->has('edit') && $isOwner,
            'isOwner'         => $isOwner,
            'publishedPages'  => $publishedPages,
        ]);
    }

    private function getPublishedPages($uid)
    {
        if (! $uid) {
            return ['home'];
        }
        // Default pages

        $pages          = ['home', 'about', 'gallery', 'contact'];
        $publishedPages = [];

        foreach ($pages as $page) {
            $content = $this->firebaseService->getUserPageContent($uid, $page);
            if ($content['published'] ?? false) {
                $publishedPages[] = $page;
            }
        }

        return $publishedPages;
    }

    /**
     * 🔥 CORRECT WEBSITE OWNER DETECTION
     */
    private function getWebsiteOwnerUid(Request $request, $currentUser)
    {
        // 1. Check URL parameter for specific user (for testing multi-user)
        // Example: http://localhost:8000?user=abc123 (view user abc123's site)
        if ($request->has('user')) {
            $requestedUid = $request->get('user');
            if ($this->userExists($requestedUid)) {
                return $requestedUid;
            }
        }

        // 2. If user is logged in, show THEIR website
        if ($currentUser) {
            return $currentUser['uid'];
        }

        // 3. DEFAULT: Show a demo site (or your own site for development)
        // This is what anonymous visitors see
        return $this->getDemoWebsiteOwner();
    }

    /**
     * Get demo website owner for anonymous visitors
     */
    private function getDemoWebsiteOwner()
    {
        // Option A: Use a dedicated demo user
        if (env('DEMO_WEBSITE_UID')) {
            return env('DEMO_WEBSITE_UID');
        }

        // Option B: Use your own UID for development (so you can see anonymous traffic)
        if (env('DEFAULT_WEBSITE_UID') && app()->environment('local')) {
            return env('DEFAULT_WEBSITE_UID');
        }

        // Option C: Get first user from database
        return $this->getFirstUserUid();
    }

    /**
     * Personalize content for website owner
     */
    private function personalizeContentForOwner($user, $page, $pageContent)
    {
        if ($page === 'home') {
            $userAdventures = $this->firebaseService->getUserAdventures($user['uid'], 4);

            if (! empty($userAdventures)) {
                $pageContent['sections']['recent']['posts'] = array_map(function ($adventure) {
                    return [
                        'title'   => $adventure['title'],
                        'excerpt' => $adventure['excerpt'],
                        'date'    => $adventure['date'],
                        'image'   => $adventure['image'],
                        'id'      => $adventure['id'],
                    ];
                }, $userAdventures);
            }

            // Update stats
            $totalAdventures                                          = count($userAdventures);
            $pageContent['sections']['mission']['stats'][0]['number'] = $totalAdventures . '+';

            $uniqueCountries                                          = count(array_unique(array_column($userAdventures, 'location')));
            $pageContent['sections']['mission']['stats'][1]['number'] = $uniqueCountries . '+';
        }

        return $pageContent;
    }

    /**
     * Get page content for specific website owner
     */
    private function getPageContentForOwner($ownerUid, $page)
    {
        if ($ownerUid) {
            $userContent = $this->firebaseService->getUserPageContent($ownerUid, $page);
            if ($userContent) {
                return $userContent;
            }
        }

        return $this->getDefaultPageContent($page);
    }

    /**
     * Get website settings for specific owner
     */
    private function getWebsiteSettingsForOwner($ownerUid)
    {
        if ($ownerUid) {
            $userSettings = $this->firebaseService->getWebsiteSettings($ownerUid);
            if ($userSettings) {
                return $userSettings;
            }
        }

        return $this->getDefaultWebsiteSettings();
    }
/**
 * Export analytics as CSV
 */
    public function exportAnalytics(Request $request, $period = '30days')
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->analyticsService->exportCSV($user['uid'], $period);
    }

/**
 * Get enhanced analytics data
 */
    public function getAnalyticsData(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $period = $request->get('period', '7days');
        $result = $this->analyticsService->getAnalyticsData($user['uid'], $period);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Get default page content for public viewing
     */
    private function getDefaultPageContent($page)
    {
        // Return default/template content for each page
        // This is what visitors see before logging in
        $defaultContent = [
            'home'    => [
                'title'    => 'Welcome to Our Adventure Blog',
                'sections' => [
                    'hero'    => [
                        'title'    => 'Explore the World',
                        'subtitle' => 'Join us on incredible journeys',
                    ],
                    'mission' => [
                        'title'       => 'Our Mission',
                        'description' => 'Sharing adventures from around the globe',
                        'stats'       => [
                            ['label' => 'Adventures', 'number' => '0+'],
                            ['label' => 'Countries', 'number' => '0+'],
                        ],
                    ],
                    'recent'  => [
                        'title' => 'Recent Adventures',
                        'posts' => [],
                    ],
                ],
            ],
            'about'   => [
                'title'    => 'About Us',
                'sections' => [
                    'story' => [
                        'title'   => 'Our Story',
                        'content' => 'We are passionate travelers...',
                    ],
                ],
            ],
            'contact' => [
                'title'    => 'Contact Us',
                'sections' => [
                    'form' => [
                        'title' => 'Get in Touch',
                        'email' => 'hello@example.com',
                    ],
                ],
            ],
            'gallery' => [
                'title'    => 'Gallery',
                'sections' => [
                    'images' => [],
                ],
            ],
        ];

        return $defaultContent[$page] ?? $defaultContent['home'];
    }

    /**
     * Get default website settings
     */
    private function getDefaultWebsiteSettings()
    {
        return [
            'siteName' => 'Adventure Blog',
            'tagline'  => 'Exploring the world one adventure at a time',
            'theme'    => 'default',
            'logo'     => null,
        ];
    }

    /**
     * API endpoint to get user's adventures (PROTECTED)
     */
    public function getUserAdventures(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $limit      = $request->get('limit', 10);
        $adventures = $this->firebaseService->getUserAdventures($user['uid'], $limit);

        return response()->json(['success' => true, 'adventures' => $adventures]);
    }

    /**
     * API endpoint to create new adventure (PROTECTED)
     */
    public function createAdventure(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'image'   => 'required|string',
            'date'    => 'required|date',
        ]);

        $result = $this->firebaseService->createAdventure($user['uid'], $request->all());

        if ($result['success']) {
            return response()->json(['success' => true, 'adventure' => $result['adventure']]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * API endpoint to update adventure (PROTECTED)
     */
    public function updateAdventure(Request $request, $adventureId)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->firebaseService->updateAdventure($user['uid'], $adventureId, $request->all());

        if ($result['success']) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * API endpoint to delete adventure (PROTECTED)
     */
    public function deleteAdventure($adventureId)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->firebaseService->deleteAdventure($user['uid'], $adventureId);

        if ($result['success']) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Update page content (PROTECTED)
     */
    public function updatePageContent(Request $request, $page)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->firebaseService->updatePageContent($user['uid'], $page, $request->all());

        if ($result['success']) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Editor page (PROTECTED - already has firebase.auth middleware)
     */
    public function editor($page = 'home')
    {
        $user = session('firebase_user');

        if (! $user) {
            return redirect('/login');
        }

        $pageContent     = $this->firebaseService->getPageContent($user['uid'], $page);
        $websiteSettings = $this->firebaseService->getWebsiteSettings($user['uid']);
        $colorPalettes   = $this->firebaseService->getColorPalettes();

        return Inertia::render('Website/Editor', [
            'user'            => $user,
            'page'            => $page,
            'pageContent'     => $pageContent,
            'websiteSettings' => $websiteSettings,
            'colorPalettes'   => $colorPalettes,
        ]);
    }

    /**
     * Check if a user exists in Firebase
     */
    private function userExists($uid)
    {
        try {
            $result = $this->firebaseService->getUser($uid);
            return $result['success'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

/**
 * Get first user UID from Firebase (fallback)
 */
    private function getFirstUserUid()
    {
        try {
            // Try to get the DEFAULT_WEBSITE_UID from env
            if (env('DEFAULT_WEBSITE_UID')) {
                return env('DEFAULT_WEBSITE_UID');
            }

            // If no default is set, return null and show generic content
            return null;
        } catch (\Exception $e) {
            \Log::error("Error getting first user UID: " . $e->getMessage());
            return null;
        }
    }
}
