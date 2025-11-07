<?php
namespace App\Services;

use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    private $database;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->database = $firebaseService->getDatabase();
    }

    /**
     * Record comprehensive visit data
     */
    public function recordVisit($uid, $page, $request)
    {
        try {
            $visitorId = $this->getVisitorId($request);
            $isUnique  = $this->isUniqueVisitor($uid, $visitorId);
            $now       = Carbon::now();

            $visitData = [
                'timestamp'  => $now->toISOString(),
                'date'       => $now->toDateString(),
                'page'       => $page,
                'visitor_id' => $visitorId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_unique'  => $isUnique,
                'session_id' => session()->getId(),
            ];

            // Record the visit
            $visitRef = $this->database->getReference("analytics/{$uid}/visits")->push($visitData);

            // Update daily, weekly, monthly summaries
            $this->updateSummaries($uid, $page, $now, $isUnique);

            // Update page views
            $this->updatePageViews($uid, $page);

            Log::info("📊 Visit recorded", ['uid' => $uid, 'page' => $page, 'unique' => $isUnique]);

            return ['success' => true];

        } catch (\Exception $error) {
            Log::error("❌ Analytics record error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Generate unique visitor ID
     */
    private function getVisitorId($request)
    {
        return md5($request->ip() . $request->userAgent());
    }

    /**
     * Check if visitor is unique
     */
    private function isUniqueVisitor($uid, $visitorId)
    {
        try {
            $ref    = $this->database->getReference("analytics/{$uid}/unique_visitors/{$visitorId}");
            $exists = $ref->getValue();

            if (! $exists) {
                // Mark as visited for 24 hours
                $ref->set([
                    'first_seen' => Carbon::now()->toISOString(),
                    'last_seen'  => Carbon::now()->toISOString(),
                ]);
                return true;
            }

            // Update last seen
            $ref->update(['last_seen' => Carbon::now()->toISOString()]);
            return false;

        } catch (\Exception $error) {
            Log::error("❌ Unique visitor check error: " . $error->getMessage());
            return true;
        }
    }

    /**
     * Update time-based summaries
     */
    private function updateSummaries($uid, $page, $date, $isUnique)
    {
        try {
            $formats = [
                'daily'   => $date->format('Y-m-d'),
                'weekly'  => $date->format('Y-W'),
                'monthly' => $date->format('Y-m'),
            ];

            foreach ($formats as $period => $periodKey) {
                $summaryRef = $this->database->getReference("analytics/{$uid}/summaries/{$period}/{$periodKey}");
                $current    = $summaryRef->getValue() ?? [
                    'total_views'     => 0,
                    'unique_visitors' => 0,
                    'pages'           => [],
                ];

                $current['total_views']++;
                if ($isUnique) {
                    $current['unique_visitors']++;
                }

                // Update page-specific data
                if (! isset($current['pages'][$page])) {
                    $current['pages'][$page] = [
                        'views'        => 0,
                        'unique_views' => 0,
                    ];
                }
                $current['pages'][$page]['views']++;
                if ($isUnique) {
                    $current['pages'][$page]['unique_views']++;
                }

                $summaryRef->set($current);
            }

        } catch (\Exception $error) {
            Log::error("❌ Summary update error: " . $error->getMessage());
        }
    }

    /**
     * Update page views counter
     */
    private function updatePageViews($uid, $page)
    {
        try {
            $pageViewsRef = $this->database->getReference("analytics/{$uid}/page_views/{$page}");
            $currentViews = $pageViewsRef->getValue() ?? 0;
            $pageViewsRef->set($currentViews + 1);

        } catch (\Exception $error) {
            Log::error("❌ Page views update error: " . $error->getMessage());
        }
    }

    /**
     * Get analytics data for dashboard
     */
    // In AnalyticsService.php
    public function getAnalyticsData($uid, $period = '7days')
    {
        \Log::info("🔍 [getAnalyticsData] Starting for UID: {$uid}, Period: {$period}");

        try {
            $endDate   = Carbon::now();
            $startDate = $this->getStartDate($period, $endDate);

            \Log::info("📅 Date range: {$startDate} to {$endDate}");

            $data = [
                'summary'       => $this->getSummaryData($uid, $startDate, $endDate),
                'time_series'   => $this->getTimeSeriesData($uid, $startDate, $endDate),
                'page_stats'    => $this->getPageStats($uid, $startDate, $endDate),
                'visitor_stats' => $this->getVisitorStats($uid, $startDate, $endDate),
            ];

            \Log::info("✅ [getAnalyticsData] Success - Summary data:", $data['summary']);

            return ['success' => true, 'data' => $data];

        } catch (\Exception $error) {
            \Log::error("❌ [getAnalyticsData] Error: " . $error->getMessage());
            \Log::error("❌ [getAnalyticsData] Stack trace: " . $error->getTraceAsString());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    private function getStartDate($period, $endDate)
    {
        return match ($period) {
            'today'  => $endDate->copy()->startOfDay(),
            '7days'  => $endDate->copy()->subDays(7),
            '30days' => $endDate->copy()->subDays(30),
            '90days' => $endDate->copy()->subDays(90),
            default  => $endDate->copy()->subDays(7)
        };
    }

    private function getSummaryData($uid, $startDate, $endDate)
    {
        try {
            $ref = $this->database->getReference("analytics/{$uid}/visits");

            // Try with index first, fallback to manual filtering if index fails
            try {
                $visits = $ref->orderByChild('timestamp')->startAt($startDate->toISOString())->getValue();
            } catch (\Exception $indexError) {
                \Log::warning("Index query failed, falling back to manual filtering: " . $indexError->getMessage());

                // Fallback: Get all visits and filter manually
                $allVisits = $ref->getValue() ?? [];
                $visits    = array_filter($allVisits, function ($visit) use ($startDate) {
                    return isset($visit['timestamp']) && Carbon::parse($visit['timestamp']) >= $startDate;
                });
            }

            // Handle null or empty visits
            if (empty($visits)) {
                return [
                    'total_views'      => 0,
                    'unique_visitors'  => 0,
                    'pages'            => [],
                    'avg_time_on_site' => 0,
                ];
            }

            $totalViews     = count($visits);
            $uniqueVisitors = collect($visits)->pluck('visitor_id')->unique()->count();
            $pages          = collect($visits)->pluck('page')->countBy();

            return [
                'total_views'      => $totalViews,
                'unique_visitors'  => $uniqueVisitors,
                'pages'            => $pages->toArray(),
                'avg_time_on_site' => 0,
            ];

        } catch (\Exception $error) {
            \Log::error("❌ getSummaryData error: " . $error->getMessage());
            return [
                'total_views'      => 0,
                'unique_visitors'  => 0,
                'pages'            => [],
                'avg_time_on_site' => 0,
            ];
        }
    }

    private function getTimeSeriesData($uid, $startDate, $endDate)
    {
        try {
            $ref = $this->database->getReference("analytics/{$uid}/summaries/daily");

            $dailyData = [];
            try {
                $dailyData = $ref->orderByKey()->startAt($startDate->format('Y-m-d'))->getValue() ?? [];
            } catch (\Exception $indexError) {
                \Log::warning("Daily summaries index query failed: " . $indexError->getMessage());

                // Fallback: Get all daily data and filter manually
                $allDailyData = $ref->getValue() ?? [];
                foreach ($allDailyData as $date => $data) {
                    if ($date >= $startDate->format('Y-m-d')) {
                        $dailyData[$date] = $data;
                    }
                }
            }

            $timeSeries = [];
            $current    = $startDate->copy();

            while ($current <= $endDate) {
                $dateKey = $current->format('Y-m-d');
                $dayData = $dailyData[$dateKey] ?? [
                    'total_views'     => 0,
                    'unique_visitors' => 0,
                ];

                $timeSeries[] = [
                    'date'            => $dateKey,
                    'views'           => $dayData['total_views'],
                    'unique_visitors' => $dayData['unique_visitors'],
                ];

                $current->addDay();
            }

            return $timeSeries;

        } catch (\Exception $error) {
            \Log::error("❌ getTimeSeriesData error: " . $error->getMessage());
            return [];
        }
    }

    private function getPageStats($uid, $startDate, $endDate)
    {
        try {
            $ref = $this->database->getReference("analytics/{$uid}/visits");

            $visits = [];
            try {
                $visits = $ref->orderByChild('timestamp')->startAt($startDate->toISOString())->getValue() ?? [];
            } catch (\Exception $indexError) {
                \Log::warning("Page stats index query failed, using manual filtering: " . $indexError->getMessage());

                // Fallback: Get all visits and filter manually
                $allVisits = $ref->getValue() ?? [];
                $visits    = array_filter($allVisits, function ($visit) use ($startDate) {
                    return isset($visit['timestamp']) && Carbon::parse($visit['timestamp']) >= $startDate;
                });
            }

            \Log::info("📊 [getPageStats] Found " . count($visits) . " visits for period");

            $pageStats = collect($visits)->groupBy('page')->map(function ($pageVisits, $page) {
                \Log::info("📄 Page '{$page}': " . count($pageVisits) . " views");
                return [
                    'page'         => $page,
                    'views'        => count($pageVisits),
                    'unique_views' => collect($pageVisits)->pluck('visitor_id')->unique()->count(),
                ];
            })->values();

            \Log::info("📈 Page stats result:", $pageStats->toArray());

            return $pageStats->toArray();

        } catch (\Exception $error) {
            \Log::error("❌ getPageStats error: " . $error->getMessage());
            return [];
        }
    }
    private function getVisitorStats($uid, $startDate, $endDate)
    {
        $ref    = $this->database->getReference("analytics/{$uid}/visits");
        $visits = $ref->orderByChild('timestamp')->startAt($startDate->toISOString())->getValue();

        $visitors = collect($visits)->groupBy('visitor_id');

        $newVisitors = $visitors->filter(function ($visits, $visitorId) use ($startDate) {
            $firstVisit = collect($visits)->min('timestamp');
            return Carbon::parse($firstVisit) >= $startDate;
        })->count();

        $returningVisitors = $visitors->count() - $newVisitors;

        return [
            'new_visitors'       => $newVisitors,
            'returning_visitors' => $returningVisitors,
            'total_visitors'     => $visitors->count(),
        ];
    }

    /**
     * Export analytics as CSV
     */
    public function exportCSV($uid, $period = '30days')
    {
        try {
            $data = $this->getAnalyticsData($uid, $period);

            if (! $data['success']) {
                return $data;
            }

            $filename = "analytics-export-{$uid}-" . Carbon::now()->format('Y-m-d') . ".csv";
            $headers  = [
                'Content-Type'        => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');

                // Summary header
                fputcsv($file, ['Analytics Summary']);
                fputcsv($file, ['Total Views', 'Unique Visitors']);
                fputcsv($file, [
                    $data['data']['summary']['total_views'],
                    $data['data']['summary']['unique_visitors'],
                ]);
                fputcsv($file, []); // Empty line

                // Time series data
                fputcsv($file, ['Date', 'Views', 'Unique Visitors']);
                foreach ($data['data']['time_series'] as $row) {
                    fputcsv($file, [$row['date'], $row['views'], $row['unique_visitors']]);
                }
                fputcsv($file, []); // Empty line

                // Page stats
                fputcsv($file, ['Page', 'Total Views', 'Unique Views']);
                foreach ($data['data']['page_stats'] as $row) {
                    fputcsv($file, [$row['page'], $row['views'], $row['unique_views']]);
                }
                fputcsv($file, []); // Empty line

                // Visitor stats
                fputcsv($file, ['New Visitors', 'Returning Visitors', 'Total Visitors']);
                fputcsv($file, [
                    $data['data']['visitor_stats']['new_visitors'],
                    $data['data']['visitor_stats']['returning_visitors'],
                    $data['data']['visitor_stats']['total_visitors'],
                ]);

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $error) {
            Log::error("❌ CSV export error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }
}
