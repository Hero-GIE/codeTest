<?php
namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GalleryController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Show gallery page with images (public)
     */
    public function show(Request $request)
    {
        $user = session('firebase_user');

        // If no user, we can still show a public gallery or redirect
        if (! $user) {
            // Option 1: Redirect to login
            // return redirect('/login');

            // Option 2: Show public gallery with sample images
            return Inertia::render('Website/Page', [
                'user'            => null,
                'page'            => 'gallery',
                'pageContent'     => [
                    'title'     => 'Adventure Gallery',
                    'images'    => [], // Empty for public view
                    'published' => true,
                ],
                'websiteSettings' => [
                    'colorPalette' => 'default',
                    'customColors' => [
                        'primary'   => '#000000',
                        'secondary' => '#8B4513',
                        'accent'    => '#FFFFFF',
                    ],
                ],
                'isEditMode'      => false,
            ]);
        }

        // Get gallery images for logged-in user
        $galleryImages = $this->firebaseService->getGalleryImages($user['uid']);

        // Get page content
        $pageContent           = $this->firebaseService->getUserPageContent($user['uid'], 'gallery');
        $pageContent['images'] = $galleryImages;

        $websiteSettings = $this->firebaseService->getWebsiteSettings($user['uid']);

        return Inertia::render('Website/Page', [
            'user'            => $user,
            'page'            => 'gallery',
            'pageContent'     => $pageContent,
            'websiteSettings' => $websiteSettings,
            'isEditMode'      => $request->has('edit'),
        ]);
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
