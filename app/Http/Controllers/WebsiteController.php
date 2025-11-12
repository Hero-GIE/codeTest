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
    // public function showPage(Request $request, $page = 'home')
    // {
    //     \Log::info("WebsiteController - showPage called", [
    //         'page' => $page,
    //         'user' => session('firebase_user') ? 'logged_in' : 'guest',
    //         'url'  => $request->fullUrl(),
    //     ]);

    //     // Get current user if logged in
    //     $user = session('firebase_user');

    //     // 🔥 CORRECT: Determine whose website we're viewing
    //     $websiteOwnerUid = $this->getWebsiteOwnerUid($request, $user);

    //     // Get content and settings for the website owner
    //     $pageContent     = $this->getPageContentForOwner($websiteOwnerUid, $page);
    //     $websiteSettings = $this->getWebsiteSettingsForOwner($websiteOwnerUid);

    //     // Make sure this includes customColors
    //     if (! $websiteSettings || ! isset($websiteSettings['customColors'])) {
    //         $websiteSettings = [
    //             'customColors' => [
    //                 'primary'   => '#000000',
    //                 'secondary' => '#8B4513',
    //                 'accent'    => '#FFFFFF',
    //             ],
    //         ];
    //     }

    //     // Determine if current user is the owner
    //     $isOwner = $user && $user['uid'] === $websiteOwnerUid;

    //     // 🔥 CHECK IF PAGE IS PUBLISHED (unless user is the owner)
    //     if (! $isOwner && ! ($pageContent['published'] ?? false)) {
    //         // Page is not published and user is not the owner
    //         abort(404, 'Page not found');
    //     }

    //     // ✅ FIXED: Load adventures separately and inject them into page content
    //     if ($page === 'home' && $websiteOwnerUid) {
    //         \Log::info("🏠 Loading adventures for home page", ['uid' => $websiteOwnerUid]);

    //         // Fetch adventures from the correct location
    //         $userAdventures = $this->firebaseService->getUserAdventuresForHomepage($websiteOwnerUid, 4);

    //         \Log::info("📊 Adventures fetched", [
    //             'count'      => count($userAdventures),
    //             'adventures' => $userAdventures,
    //         ]);

    //         // Inject adventures into page content
    //         if (! isset($pageContent['sections'])) {
    //             $pageContent['sections'] = [];
    //         }
    //         if (! isset($pageContent['sections']['recent'])) {
    //             $pageContent['sections']['recent'] = [
    //                 'title' => 'Recent Adventures',
    //                 'posts' => [],
    //             ];
    //         }

    //         $pageContent['sections']['recent']['posts'] = $userAdventures;

    //         \Log::info("✅ Adventures injected into page content", [
    //             'final_count' => count($pageContent['sections']['recent']['posts']),
    //         ]);
    //     }

    //     // 🔥 GALLERY-SPECIFIC LOGIC: Load gallery images
    //     if ($page === 'gallery' && $websiteOwnerUid) {
    //         \Log::info("🖼️ Loading gallery images for gallery page", ['uid' => $websiteOwnerUid]);

    //         // Fetch gallery images from Firebase
    //         $galleryImages = $this->firebaseService->getGalleryImages($websiteOwnerUid);

    //         \Log::info("📊 Gallery images fetched", [
    //             'count'  => count($galleryImages),
    //             'images' => array_map(function ($img) {
    //                 return [
    //                     'id'      => $img['id'] ?? 'unknown',
    //                     'caption' => $img['caption'] ?? 'No caption',
    //                 ];
    //             }, $galleryImages),
    //         ]);

    //         // Inject gallery images into page content
    //         $pageContent['images'] = $galleryImages;

    //         \Log::info("✅ Gallery images injected into page content", [
    //             'final_count' => count($pageContent['images']),
    //         ]);
    //     }

    //     // 🔥 RECORD ANALYTICS for the website owner (only for published pages)
    //     if ($websiteOwnerUid && ($pageContent['published'] ?? false)) {
    //         $this->analyticsService->recordVisit($websiteOwnerUid, $page, $request);
    //     }

    //     // Get list of published pages
    //     $publishedPages = $this->getPublishedPages($websiteOwnerUid);

    //     \Log::info("📋 Final published pages for navigation", [
    //         'uid'             => $websiteOwnerUid,
    //         'published_pages' => $publishedPages,
    //         'current_page'    => $page,
    //         'is_published'    => in_array($page, $publishedPages),
    //     ]);

    //     return Inertia::render('Website/Page', [
    //         'user'            => $user,
    //         'page'            => $page,
    //         'pageContent'     => $pageContent,
    //         'websiteSettings' => $websiteSettings,
    //         'isEditMode'      => $request->has('edit') && $isOwner,
    //         'isOwner'         => $isOwner,
    //         'publishedPages'  => $publishedPages,
    //     ]);
    // }

    public function showPage(Request $request, $page = 'home')
    {
        \Log::info("WebsiteController - showPage called", [
            'page' => $page,
            'user' => session('firebase_user') ? 'logged_in' : 'guest',
            'url'  => $request->fullUrl(),
        ]);

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

        // ✅ ADD GALLERY DEBUG LOGGING RIGHT HERE - before the gallery-specific logic
        if ($page === 'gallery') {
            \Log::info("🖼️ Gallery Page Debug - BEFORE Processing", [
                'website_owner_uid'      => $websiteOwnerUid,
                'page_content_exists'    => ! empty($pageContent),
                'has_sections'           => isset($pageContent['sections']),
                'has_hero'               => isset($pageContent['sections']['hero']),
                'hero_keys'              => isset($pageContent['sections']['hero']) ? array_keys($pageContent['sections']['hero']) : 'no hero',
                'is_using_default'       => $pageContent === $this->getDefaultPageContent('gallery'),
                'page_content_structure' => $pageContent, // This will show the full structure
            ]);
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

        // 🔥 GALLERY-SPECIFIC LOGIC: Load gallery images
        if ($page === 'gallery' && $websiteOwnerUid) {
            \Log::info("🖼️ Loading gallery images for gallery page", ['uid' => $websiteOwnerUid]);

            // Fetch gallery images from Firebase
            $galleryImages = $this->firebaseService->getGalleryImages($websiteOwnerUid);

            \Log::info("📊 Gallery images fetched", [
                'count'  => count($galleryImages),
                'images' => array_map(function ($img) {
                    return [
                        'id'      => $img['id'] ?? 'unknown',
                        'caption' => $img['caption'] ?? 'No caption',
                    ];
                }, $galleryImages),
            ]);

            // Inject gallery images into page content
            $pageContent['images'] = $galleryImages;

            \Log::info("✅ Gallery images injected into page content", [
                'final_count' => count($pageContent['images']),
            ]);

            // ✅ ADD SECOND DEBUG LOG AFTER GALLERY PROCESSING
            \Log::info("🖼️ Gallery Page Debug - AFTER Processing", [
                'has_images'              => isset($pageContent['images']),
                'images_count'            => count($pageContent['images'] ?? []),
                'final_page_content_keys' => array_keys($pageContent),
            ]);
        }

        // 🔥 RECORD ANALYTICS for the website owner (only for published pages)
        if ($websiteOwnerUid && ($pageContent['published'] ?? false)) {
            $this->analyticsService->recordVisit($websiteOwnerUid, $page, $request);
        }

        // Get list of published pages
        $publishedPages = $this->getPublishedPages($websiteOwnerUid);

        \Log::info("📋 Final published pages for navigation", [
            'uid'             => $websiteOwnerUid,
            'published_pages' => $publishedPages,
            'current_page'    => $page,
            'is_published'    => in_array($page, $publishedPages),
        ]);

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
        $defaultContent = [
            'home'    => [
                'title'     => 'My Adventure Log',
                'published' => true,
                'sections'  => [
                    'hero'     => [
                        'title'           => 'Welcome to My Adventure Log',
                        'subtitle'        => 'Documenting my journeys and experiences',
                        'badge'           => 'Welcome to Your Adventure Log',
                        'backgroundImage' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80',
                        'cta1Title'       => 'Explore Adventures',
                        'cta1Subtitle'    => 'Start your journey today',
                        'cta2Title'       => 'We guide your path',
                        'cta2Subtitle'    => 'Get into action',
                        'stats'           => [
                            [
                                'number'      => '0+',
                                'label'       => 'Adventures Logged',
                                'description' => 'By our community of explorers',
                            ],
                            [
                                'number'      => '0+',
                                'label'       => 'Countries Covered',
                                'description' => 'By our community of explorers',
                            ],
                            [
                                'number'      => '0+',
                                'label'       => 'Photos Shared',
                                'description' => 'By our community of explorers',
                            ],
                        ],
                    ],
                    'features' => [
                        'title' => 'What I Do',
                        'items' => [
                            [
                                'title'       => 'Adventure Logging',
                                'description' => 'Track and share my adventures',
                                'icon'        => '🚀',
                            ],
                            [
                                'title'       => 'Story Telling',
                                'description' => 'Share experiences and memories',
                                'icon'        => '📖',
                            ],
                            [
                                'title'       => 'Photo Journal',
                                'description' => 'Visual journey through photos',
                                'icon'        => '📷',
                            ],
                        ],
                    ],
                    'mission'  => [
                        'title'   => 'Our Mission',
                        'content' => 'We believe every adventure has a story worth telling. Our mission is to provide the tools and platform for adventurers to document, share, and inspire others with their journeys.',
                        'stats'   => [
                            ['number' => '0+', 'label' => 'Adventures Logged'],
                            ['number' => '0+', 'label' => 'Countries Covered'],
                            ['number' => '0+', 'label' => 'Photos Shared'],
                            ['number' => '1', 'label' => 'Happy Explorer'],
                        ],
                    ],
                    'recent'   => [
                        'title' => 'Recent Adventures',
                        'posts' => [
                            [
                                'title'   => 'Mountain Hiking',
                                'date'    => '2024-01-15',
                                'image'   => 'https://images.unsplash.com/photo-1454496522488-7a8e488e8606?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1176&q=80',
                                'excerpt' => 'Amazing views from the summit...',
                            ],
                            [
                                'title'   => 'Beach Vacation',
                                'date'    => '2024-01-10',
                                'image'   => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1173&q=80',
                                'excerpt' => 'Relaxing days by the ocean...',
                            ],
                        ],
                    ],
                ],
            ],
            'about'   => [
                'title'     => 'About Our Adventure',
                'published' => true,
                'sections'  => [
                    'hero'         => [
                        'title'    => 'About Us',
                        'subtitle' => 'Discover the story behind Adventure Log and the passionate team dedicated to helping you document and share your journeys with the world.',
                    ],
                    'mission'      => [
                        'title'       => 'OUR MISSION',
                        'heading'     => 'Empowering Adventurers Worldwide',
                        'points'      => [
                            'Born from a passion for exploration and storytelling, Adventure Log was created to bridge the gap between memorable experiences and lasting documentation.',
                            'We understand that every journey, whether it\'s climbing mountains or exploring local hidden gems, deserves to be remembered and shared in a beautiful, meaningful way.',
                            'Our platform combines intuitive design with powerful features to help you create stunning visual narratives of your adventures.',
                        ],
                        'quote'       => '"Every adventure is a story waiting to be told. We\'re here to help you tell yours in the most beautiful way possible."',
                        'quoteAuthor' => '— The Adventure Log Team',
                    ],
                    'featureCards' => [
                        [
                            'title'       => 'Global Community',
                            'description' => 'Join adventurers from around the world sharing their incredible stories and inspiring others to explore.',
                            'icon'        => 'faGlobeAmericas',
                        ],
                        [
                            'title'       => 'Innovative Platform',
                            'description' => 'Cutting-edge tools and features designed specifically for documenting and sharing your adventures beautifully.',
                            'icon'        => 'faCompass',
                        ],
                        [
                            'title'       => 'Built with Passion',
                            'description' => 'Created by adventurers, for adventurers. We live and breathe exploration and understand your needs.',
                            'icon'        => 'faHeart',
                        ],
                    ],
                ],
                'stats'     => [
                    'team_members'      => '5K+',
                    'countries_reached' => '50+',
                    'years_of_passion'  => '3+',
                ],
            ],
            'gallery' => [
                'title'     => 'Adventure Gallery',
                'published' => true,
                'images'    => [],
            ],
            'contact' => [
                'title'     => 'Get In Touch',
                'published' => true,
                'email'     => 'hello@example.com',
                'social'    => [
                    'instagram' => '@myadventures',
                    'twitter'   => '@adventurelog',
                    'facebook'  => 'myadventurepage',
                ],
                'sections'  => [
                    'hero'   => [
                        'title'    => 'Get In Touch',
                        'subtitle' => 'We\'d love to hear about your adventures and help you share them with the world',
                    ],
                    'info'   => [
                        'title'       => 'Let\'s Start a Conversation',
                        'description' => 'Whether you have questions about documenting your adventures, need technical support, or just want to share an amazing story, we\'re here to help.',
                    ],
                    'social' => [
                        'title' => 'Follow Our Adventures',
                    ],
                    'faq'    => [
                        'title'       => 'Frequently Asked Questions',
                        'description' => 'Quick answers to common questions',
                        'items'       => [
                            [
                                'q'    => 'How do I start documenting my adventures?',
                                'a'    => 'Simply create an account and start adding your first adventure story with photos and descriptions.',
                                'icon' => 'faMapMarkedAlt',
                            ],
                            [
                                'q'    => 'Is there a mobile app?',
                                'a'    => 'Yes! Our mobile app lets you document adventures on the go with real-time photo uploads.',
                                'icon' => 'faMobileAlt',
                            ],
                            [
                                'q'    => 'Can I collaborate with friends?',
                                'a'    => 'Absolutely! You can create shared adventure logs with multiple contributors.',
                                'icon' => 'faUsers',
                            ],
                            [
                                'q'    => 'Is my data secure?',
                                'a'    => 'We use enterprise-grade security to protect your stories and personal information.',
                                'icon' => 'faShieldAlt',
                            ],
                        ],
                    ],
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
