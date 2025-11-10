<?php
namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GalleryController extends Controller
{
    protected $firebaseService;
    protected $analyticsService; // Add this property

    public function __construct(FirebaseService $firebaseService, AnalyticsService $analyticsService)
    {
        $this->firebaseService  = $firebaseService;
        $this->analyticsService = $analyticsService;
    }

    // In GalleryController.php - Update the show method
    public function show(Request $request)
    {
        $user = session('firebase_user');

        // 🔥 IMPORTANT: Use the SAME logic as WebsiteController
        $websiteOwnerUid = $this->getWebsiteOwnerUid($request, $user);

        // Get page content for the website owner
        $pageContent = $this->firebaseService->getUserPageContent($websiteOwnerUid ?: 'default', 'gallery');

        // Determine if current user is the owner
        $isOwner = $user && $user['uid'] === $websiteOwnerUid;

        // 🔥 FIX: Use the EXACT same method as WebsiteController
        $publishedPages = $this->getPublishedPages($websiteOwnerUid);

        // 🔥 CHECK IF GALLERY IS PUBLISHED (unless user is the owner)
        if (! $isOwner && ! ($pageContent['published'] ?? false)) {
            abort(404, 'Page not found');
        }

        // 🔥 RECORD ANALYTICS for the website owner (only if published)
        if ($websiteOwnerUid && ($pageContent['published'] ?? false)) {
            $this->analyticsService->recordVisit($websiteOwnerUid, 'gallery', $request);
        }

        // Get gallery images for the website owner
        $galleryImages         = $this->firebaseService->getGalleryImages($websiteOwnerUid);
        $pageContent['images'] = $galleryImages;

        $websiteSettings = $this->firebaseService->getWebsiteSettings($websiteOwnerUid);

        // 🔥 CRITICAL: Make sure websiteSettings has customColors
        if (! $websiteSettings || ! isset($websiteSettings['customColors'])) {
            $websiteSettings = [
                'customColors' => [
                    'primary'   => '#000000',
                    'secondary' => '#8B4513',
                    'accent'    => '#FFFFFF',
                ],
            ];
        }

        return Inertia::render('Website/Page', [
            'user'            => $user,
            'page'            => 'gallery',
            'pageContent'     => $pageContent,
            'websiteSettings' => $websiteSettings,
            'isEditMode'      => $request->has('edit') && $isOwner,
            'isOwner'         => $isOwner,
            'publishedPages'  => $publishedPages, // ✅ Now consistent with other pages
        ]);
    }

// Add this method to GalleryController
    private function getPublishedPages($uid)
    {
        if (! $uid) {
            return ['home'];
        }

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
     * 🔥 CORRECT WEBSITE OWNER DETECTION (copied from WebsiteController)
     */
    private function getWebsiteOwnerUid(Request $request, $currentUser)
    {
        // 1. Check URL parameter for specific user
        if ($request->has('user')) {
            $requestedUid = $request->get('user');
            // You might want to verify user exists here
            return $requestedUid;
        }

        // 2. If user is logged in, show THEIR website
        if ($currentUser) {
            return $currentUser['uid'];
        }

        // 3. DEFAULT: Show a demo site
        return $this->getDemoWebsiteOwner();
    }

    /**
     * Get demo website owner for anonymous visitors
     */
    private function getDemoWebsiteOwner()
    {
        // Use environment variable or first user
        if (env('DEMO_WEBSITE_UID')) {
            return env('DEMO_WEBSITE_UID');
        }

        if (env('DEFAULT_WEBSITE_UID') && app()->environment('local')) {
            return env('DEFAULT_WEBSITE_UID');
        }

        return null;
    }
    /**
     * Upload gallery image (protected)
     */
    public function uploadImage(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'image'    => 'required|image|max:5120', // 5MB max
            'caption'  => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $result = $this->firebaseService->uploadGalleryImage(
            $user['uid'],
            $request->file('image'),
            [
                'caption'  => $request->caption,
                'location' => $request->location,
            ]
        );

        if ($result['success']) {
            return response()->json([
                'success'      => true,
                'image'        => $result['image'],
                'firebase_key' => $result['firebase_key'],
                'message'      => 'Image uploaded successfully!',
            ]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Delete gallery image (protected)
     */
    public function deleteImage(Request $request, $publitioId)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->firebaseService->deleteGalleryImage(
            $user['uid'],
            $publitioId,
            $request->get('firebase_key')
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully!',
            ]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Update gallery image metadata (protected)
     */
    public function updateImage(Request $request, $imageId)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'caption'  => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);

        $result = $this->firebaseService->updateGalleryImage(
            $user['uid'],
            $imageId,
            $request->only(['caption', 'location'])
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Image updated successfully!',
            ]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Get gallery images for API (protected)
     */
    public function getImages(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $images = $this->firebaseService->getGalleryImages($user['uid']);

        return response()->json([
            'success' => true,
            'images'  => $images,
        ]);
    }
}
