<?php
namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $firebaseService;
    protected $analyticsService;

    public function __construct(FirebaseService $firebaseService, AnalyticsService $analyticsService)
    {
        $this->firebaseService  = $firebaseService;
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        $user = session('firebase_user');

        if (! $user) {
            return redirect('/login');
        }

        // Get initial analytics data for the dashboard
        $analyticsData = $this->analyticsService->getAnalyticsData($user['uid'], '30days');

        $websiteSettings = $this->firebaseService->getWebsiteSettings($user['uid']);
        $colorPalettes   = $this->firebaseService->getColorPalettes();

        // Get user's recent adventures for dashboard
        $recentAdventures = $this->firebaseService->getUserAdventures($user['uid'], 5);

        // Get all pages status
        $pages      = ['home', 'about', 'gallery', 'contact'];
        $pageStatus = [];
        foreach ($pages as $page) {
            $pageContent       = $this->firebaseService->getUserPageContent($user['uid'], $page);
            $pageStatus[$page] = [
                'published' => $pageContent['published'] ?? false,
                'title'     => $pageContent['title'] ?? ucfirst($page),
            ];
        }

        return Inertia::render('Dashboard', [
            'user'             => $user,
            'analytics'        => $analyticsData,
            'websiteSettings'  => $websiteSettings,
            'colorPalettes'    => $colorPalettes,
            'pageStatus'       => $pageStatus,
            'recentAdventures' => $recentAdventures,
        ]);
    }

    /**
     * Get analytics data (API endpoint) - FIXED METHOD NAME
     */
    // In DashboardController.php
    public function getAnalytics(Request $request)
    {
        \Log::info("🔍 [DashboardController::getAnalytics] Method called");

        $user = session('firebase_user');
        \Log::info("👤 User session:", $user ? ['uid' => $user['uid']] : ['user' => 'null']);

        if (! $user) {
            \Log::warning("❌ Unauthorized access to analytics");
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $period = $request->get('period', '7days');
        \Log::info("📅 Requested period: {$period}");

        try {
            $result = $this->analyticsService->getAnalyticsData($user['uid'], $period);
            \Log::info("✅ Analytics service result:", ['success' => $result['success']]);

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error("❌ Exception in getAnalytics: " . $e->getMessage());
            \Log::error("❌ Stack trace: " . $e->getTraceAsString());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Export analytics as CSV
     */
    public function exportAnalytics(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $period = $request->get('period', '30days');
        return $this->analyticsService->exportCSV($user['uid'], $period);
    }

    public function editor($page = 'home')
    {
        $user = session('firebase_user');

        if (! $user) {
            return redirect('/login');
        }

        $pageContent     = $this->firebaseService->getPageContent($user['uid'], $page);
        $colorPalettes   = $this->firebaseService->getColorPalettes();
        $websiteSettings = $this->firebaseService->getWebsiteSettings($user['uid']);

        return Inertia::render('Website/Editor', [
            'user'            => $user,
            'page'            => $page,
            'pageContent'     => $pageContent,
            'colorPalettes'   => $colorPalettes,
            'websiteSettings' => $websiteSettings,
        ]);
    }

    public function updatePageContent(Request $request, $page)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->firebaseService->updatePageContent(
            $user['uid'],
            $page,
            $request->all()
        );

        if ($result['success']) {
            return redirect()->back()->with('success', 'Page updated successfully!');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    public function updateWebsiteSettings(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $result = $this->firebaseService->updateWebsiteSettings(
            $user['uid'],
            $request->all()
        );

        if ($result['success']) {
            return redirect()->back()->with('success', 'Website settings updated successfully!');
        }

        return redirect()->back()->with('error', $result['error']);
    }

    public function uploadImage(Request $request)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $result = $this->firebaseService->uploadImage(
            $user['uid'],
            $request->file('image')
        );

        if ($result['success']) {
            return response()->json(['success' => true, 'url' => $result['url']]);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    public function togglePublish(Request $request, $page)
    {
        $user = session('firebase_user');

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $published = $request->boolean('published');
        $result    = $this->firebaseService->togglePublishPage($user['uid'], $page, $published);

        if ($result['success']) {
            return response()->json(['success' => true, 'published' => $published]);
        }

        return response()->json(['error' => $result['error']], 400);
    }
}
