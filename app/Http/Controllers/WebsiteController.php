<?php
namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function showPage(Request $request, $page = 'home')
    {
        // Get current user if logged in
        $user = session('firebase_user');

        // 🔥 CORRECT: Determine whose website we're viewing
        $websiteOwnerUid = $this->getWebsiteOwnerUid($request, $user);

        // Get content and settings for the website owner
        $pageContent     = $this->getPageContentForOwner($websiteOwnerUid, $page);
        $websiteSettings = $this->getWebsiteSettingsForOwner($websiteOwnerUid);

        // Make sure this includes customColors
        if (! $websiteSettings || ! isset($websiteSettings['customColors'])) {
            $websiteSettings = [
                'customColors' => [
                    'primary'   => '#000000',
                    'secondary' => '#8B4513',
                    'accent'    => '#FFFFFF',
                ],
            ];
        }

        // Determine if current user is the owner
        $isOwner = $user && $user['uid'] === $websiteOwnerUid;

        // 🔥 CHECK IF PAGE IS PUBLISHED (unless user is the owner)
        if (! $isOwner && ! ($pageContent['published'] ?? false)) {
            // Page is not published and user is not the owner
            abort(404, 'Page not found');
        }

        // ✅ FIXED: Load adventures separately and inject them into page content
        if ($page === 'home' && $websiteOwnerUid) {
            \Log::info("🏠 Loading adventures for home page", ['uid' => $websiteOwnerUid]);

            // Fetch adventures from the correct location
            $userAdventures = $this->firebaseService->getUserAdventuresForHomepage($websiteOwnerUid, 4);

            \Log::info("📊 Adventures fetched", [
                'count'      => count($userAdventures),
                'adventures' => $userAdventures,
            ]);

            // Inject adventures into page content
            if (! isset($pageContent['sections'])) {
                $pageContent['sections'] = [];
            }
            if (! isset($pageContent['sections']['recent'])) {
                $pageContent['sections']['recent'] = [
                    'title' => 'Recent Adventures',
                    'posts' => [],
                ];
            }

            $pageContent['sections']['recent']['posts'] = $userAdventures;

            \Log::info("✅ Adventures injected into page content", [
                'final_count' => count($pageContent['sections']['recent']['posts']),
            ]);
        }

        // 🔥 RECORD ANALYTICS for the website owner (only for published pages)
        if ($websiteOwnerUid && ($pageContent['published'] ?? false)) {
            $this->analyticsService->recordVisit($websiteOwnerUid, $page, $request);
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
            $userAdventures = $this->firebaseService->getUserAdventuresForHomepage($user['uid'], 4);

            if (! empty($userAdventures)) {
                $pageContent['sections']['recent']['posts'] = $userAdventures;
            }

            // Update stats based on actual adventures
            $totalAdventures = count($userAdventures);
            if (isset($pageContent['sections']['mission']['stats'][0])) {
                $pageContent['sections']['mission']['stats'][0]['number'] = $totalAdventures . '+';
            }

            $uniqueCountries = count(array_unique(array_column($userAdventures, 'location')));
            if (isset($pageContent['sections']['mission']['stats'][1])) {
                $pageContent['sections']['mission']['stats'][1]['number'] = $uniqueCountries . '+';
            }
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
        \Log::info("🔍 [getUserAdventures] Starting adventure fetch request");

        $user = session('firebase_user');

        if (! $user) {
            \Log::warning("⚠️ [getUserAdventures] Unauthorized access - no user in session");
            \Log::debug("📊 [getUserAdventures] Session data:", session()->all());
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        \Log::info("👤 [getUserAdventures] User authenticated", [
            'uid'   => $user['uid'],
            'email' => $user['email'] ?? 'unknown',
        ]);

        $limit = $request->get('limit', 10);
        \Log::info("📋 [getUserAdventures] Fetching adventures", [
            'limit'          => $limit,
            'request_params' => $request->all(),
        ]);

        try {
            $adventures = $this->firebaseService->getUserAdventures($user['uid'], $limit);

            \Log::info("✅ [getUserAdventures] Successfully fetched adventures", [
                'count'            => count($adventures),
                'adventure_titles' => array_column($adventures, 'title'),
            ]);

            return response()->json([
                'success'    => true,
                'adventures' => $adventures,
                'count'      => count($adventures),
            ]);

        } catch (\Exception $e) {
            \Log::error("❌ [getUserAdventures] Exception while fetching adventures", [
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
                'user_uid' => $user['uid'],
                'limit'    => $limit,
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Failed to fetch adventures',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

/**
 * API endpoint to create new adventure (PROTECTED)
 */
    public function createAdventure(Request $request)
    {
        \Log::info("🚀 [createAdventure] Starting adventure creation");

        $user = session('firebase_user');

        if (! $user) {
            \Log::warning("⚠️ [createAdventure] Unauthorized access - no user in session");
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        \Log::info("👤 [createAdventure] User authenticated", [
            'uid'   => $user['uid'],
            'email' => $user['email'] ?? 'unknown',
        ]);

        // Validate request data
        $validator = Validator::make($request->all(), [
            'title'   => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'image'   => 'required|string',
            'date'    => 'required|date',
        ]);

        if ($validator->fails()) {
            \Log::warning("⚠️ [createAdventure] Validation failed", [
                'errors'     => $validator->errors()->toArray(),
                'input_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        \Log::info("📝 [createAdventure] Validation passed", [
            'title' => $request->title,
            'date'  => $request->date,
        ]);

        try {
            $result = $this->firebaseService->createAdventure($user['uid'], $request->all());

            if ($result['success']) {
                \Log::info("✅ [createAdventure] Adventure created successfully", [
                    'adventure_id' => $result['id'],
                    'title'        => $request->title,
                    'user_uid'     => $user['uid'],
                ]);

                return response()->json([
                    'success'   => true,
                    'adventure' => $result['adventure'],
                    'message'   => 'Adventure created successfully',
                ]);
            } else {
                \Log::error("❌ [createAdventure] Firebase service returned error", [
                    'error'          => $result['error'],
                    'user_uid'       => $user['uid'],
                    'adventure_data' => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'error'   => $result['error'] ?? 'Failed to create adventure',
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error("❌ [createAdventure] Exception while creating adventure", [
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
                'user_uid'       => $user['uid'],
                'adventure_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Failed to create adventure',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

/**
 * API endpoint to update adventure (PROTECTED)
 */
    public function updateAdventure(Request $request, $adventureId)
    {
        \Log::info("🔄 [updateAdventure] Starting adventure update", [
            'adventure_id' => $adventureId,
        ]);

        $user = session('firebase_user');

        if (! $user) {
            \Log::warning("⚠️ [updateAdventure] Unauthorized access - no user in session");
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        \Log::info("👤 [updateAdventure] User authenticated", [
            'uid'   => $user['uid'],
            'email' => $user['email'] ?? 'unknown',
        ]);

        if (empty($adventureId)) {
            \Log::warning("⚠️ [updateAdventure] Missing adventure ID");
            return response()->json([
                'success' => false,
                'error'   => 'Adventure ID is required',
            ], 400);
        }

        \Log::info("📝 [updateAdventure] Update data received", [
            'adventure_id' => $adventureId,
            'update_data'  => $request->all(),
        ]);

        try {
            $result = $this->firebaseService->updateAdventure($user['uid'], $adventureId, $request->all());

            if ($result['success']) {
                \Log::info("✅ [updateAdventure] Adventure updated successfully", [
                    'adventure_id' => $adventureId,
                    'user_uid'     => $user['uid'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Adventure updated successfully',
                ]);
            } else {
                \Log::error("❌ [updateAdventure] Firebase service returned error", [
                    'error'        => $result['error'],
                    'user_uid'     => $user['uid'],
                    'adventure_id' => $adventureId,
                    'update_data'  => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'error'   => $result['error'] ?? 'Failed to update adventure',
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error("❌ [updateAdventure] Exception while updating adventure", [
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
                'user_uid'     => $user['uid'],
                'adventure_id' => $adventureId,
                'update_data'  => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Failed to update adventure',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint to delete adventure (PROTECTED)
     */
    public function deleteAdventure($adventureId)
    {
        \Log::info("🗑️ [deleteAdventure] Starting adventure deletion", [
            'adventure_id' => $adventureId,
        ]);

        $user = session('firebase_user');

        if (! $user) {
            \Log::warning("⚠️ [deleteAdventure] Unauthorized access - no user in session");
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        \Log::info("👤 [deleteAdventure] User authenticated", [
            'uid'   => $user['uid'],
            'email' => $user['email'] ?? 'unknown',
        ]);

        if (empty($adventureId)) {
            \Log::warning("⚠️ [deleteAdventure] Missing adventure ID");
            return response()->json([
                'success' => false,
                'error'   => 'Adventure ID is required',
            ], 400);
        }

        try {
            $result = $this->firebaseService->deleteAdventure($user['uid'], $adventureId);

            if ($result['success']) {
                \Log::info("✅ [deleteAdventure] Adventure deleted successfully", [
                    'adventure_id' => $adventureId,
                    'user_uid'     => $user['uid'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Adventure deleted successfully',
                ]);
            } else {
                \Log::error("❌ [deleteAdventure] Firebase service returned error", [
                    'error'        => $result['error'],
                    'user_uid'     => $user['uid'],
                    'adventure_id' => $adventureId,
                ]);

                return response()->json([
                    'success' => false,
                    'error'   => $result['error'] ?? 'Failed to delete adventure',
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error("❌ [deleteAdventure] Exception while deleting adventure", [
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
                'user_uid'     => $user['uid'],
                'adventure_id' => $adventureId,
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Failed to delete adventure',
                'message' => $e->getMessage(),
            ], 500);
        }
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
