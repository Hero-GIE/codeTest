<?php
namespace App\Services;

use Kreait\Firebase\Auth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Factory;

class FirebaseService
{
    private $auth;
    private $database;
    private $firebase;

    public function __construct()
    {
        $this->initializeFirebase();
    }

    private function initializeFirebase()
    {
        $serviceAccount = [
            "type"                        => "service_account",
            "project_id"                  => env('FIREBASE_PROJECT_ID'),
            "private_key_id"              => env('FIREBASE_PRIVATE_KEY_ID'),
            "private_key"                 => str_replace('\\n', "\n", env('FIREBASE_PRIVATE_KEY')),
            "client_email"                => env('FIREBASE_CLIENT_EMAIL'),
            "client_id"                   => env('FIREBASE_CLIENT_ID'),
            "auth_uri"                    => "https://accounts.google.com/o/oauth2/auth",
            "token_uri"                   => "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url" => "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"        => env('FIREBASE_CLIENT_CERT_URL'),
        ];

        $this->firebase = (new Factory)
            ->withServiceAccount($serviceAccount)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->auth     = $this->firebase->createAuth();
        $this->database = $this->firebase->createDatabase();
    }

/**
 * Check if user exists in Realtime Database - FIXED with fallback
 */
    public function checkUserExists($email)
    {
        try {
            \Log::info("🔍 [checkUserExists] Starting check for: " . $email);

            $usersRef = $this->database->getReference('users');

            // Try with index first
            try {
                $query    = $usersRef->orderByChild('email')->equalTo($email);
                $snapshot = $query->getSnapshot();
                $data     = $snapshot->getValue();

                \Log::info("📊 [checkUserExists] Query result: " . json_encode($data));

                $userExists = ! empty($data);
                \Log::info("✅ [checkUserExists] User exists: " . ($userExists ? 'true' : 'false'));

                return $userExists;
            } catch (\Exception $indexError) {
                \Log::warning("⚠️ [checkUserExists] Index query failed, falling back to manual scan: " . $indexError->getMessage());

                // Fallback: Get all users and filter manually
                $allUsers   = $usersRef->getValue() ?? [];
                $userExists = false;

                foreach ($allUsers as $userData) {
                    if (isset($userData['email']) && $userData['email'] === $email) {
                        $userExists = true;
                        break;
                    }
                }

                \Log::info("✅ [checkUserExists] Manual scan result: " . ($userExists ? 'found' : 'not found'));
                return $userExists;
            }

        } catch (\Exception $error) {
            \Log::error("❌ [checkUserExists] Error: " . $error->getMessage());
            return false;
        }
    }

    // In FirebaseService.php - Update this method

    public function getUserAdventuresForHomepage($uid)
    {
        try {
            \Log::info("🔍 [getUserAdventuresForHomepage] Fetching adventures for UID: {$uid}");

            // ✅ CORRECT PATH: Adventures are stored in home page sections
            $ref = $this->database->getReference("websites/{$uid}/pages/home/sections/recent/posts");

            // Get all adventures
            $allAdventures = $ref->getValue();

            \Log::info("📊 [getUserAdventuresForHomepage] Raw adventures data", [
                'path' => "websites/{$uid}/pages/home/sections/recent/posts",
                'count' => $allAdventures ? count($allAdventures) : 0,
                'data'  => $allAdventures,
            ]);

            if (empty($allAdventures)) {
                \Log::info("ℹ️ [getUserAdventuresForHomepage] No adventures found for user: {$uid}");
                return [];
            }

            // Convert to array and process
            $adventuresArray = [];
            foreach ($allAdventures as $key => $adventure) {
                // Skip if adventure is not an array
                if (! is_array($adventure)) {
                    \Log::warning("⚠️ [getUserAdventuresForHomepage] Skipping non-array adventure: " . $key);
                    continue;
                }

                // All adventures in recent/posts are considered published
                $adventuresArray[] = [
                    'id'        => $key, // Use Firebase key as ID
                    'title'     => $adventure['title'] ?? 'Untitled Adventure',
                    'excerpt'   => $adventure['excerpt'] ?? '',
                    'image'     => $adventure['image'] ?? '',
                    'date'      => $adventure['date'] ?? '',
                    'location'  => $adventure['location'] ?? '',
                    'createdAt' => $adventure['createdAt'] ?? '',
                    'published' => true, // All adventures in recent section are published
                ];
            }

            \Log::info("✅ [getUserAdventuresForHomepage] Processed adventures", [
                'total_found' => count($adventuresArray),
                'titles'      => array_column($adventuresArray, 'title'),
            ]);

            if (empty($adventuresArray)) {
                \Log::info("ℹ️ [getUserAdventuresForHomepage] No adventures after processing");
                return [];
            }

            // Sort by date descending (newest first)
            usort($adventuresArray, function ($a, $b) {
                $dateA = $a['date'] ?: $a['createdAt'];
                $dateB = $b['date'] ?: $b['createdAt'];

                // Handle empty dates
                if (empty($dateA)) {
                    return 1;
                }

                if (empty($dateB)) {
                    return -1;
                }

                return strtotime($dateB) - strtotime($dateA);
            });

            // ✅ REMOVED THE LIMIT - return all adventures
            \Log::info("✅ [getUserAdventuresForHomepage] Final adventures to return", [
                'count'      => count($adventuresArray),
                'adventures' => $adventuresArray,
            ]);

            return $adventuresArray;
        } catch (\Exception $error) {
            \Log::error("❌ [getUserAdventuresForHomepage] Error: " . $error->getMessage());
            \Log::error("❌ Stack trace: " . $error->getTraceAsString());
            return [];
        }
    }
    /**
     * Register new user
     */
    public function register($email, $password, $userData = [])
    {
        \Log::info("\n🚀 ========== REGISTRATION START ==========");

        try {
            // Step 1: Check if user exists
            \Log::info("📍 STEP 1: Quick check if email might be registered...");

            // Step 2: Create Firebase Auth user
            \Log::info("📍 STEP 2: Creating Firebase Auth user...");

            $userProperties = [
                'email'         => $email,
                'emailVerified' => false,
                'password'      => $password,
                'displayName'   => $userData['name'] ?? explode('@', $email)[0],
            ];

            $user = $this->auth->createUser($userProperties);

            \Log::info("✅ Auth user created - UID: " . $user->uid);

            // Step 3: Save to Realtime Database
            \Log::info("📍 STEP 3: Saving to Realtime Database...");

            $userProfile = [
                'uid'       => $user->uid,
                'email'     => $user->email,
                'name'      => $userData['name'] ?? $user->displayName ?? explode('@', $email)[0],
                'createdAt' => date('c'),
                'updatedAt' => date('c'),
            ] + $userData;

            $dbSaveSuccess = false;

            try {
                $dbRef = $this->database->getReference('users/' . $user->uid);
                $dbRef->set($userProfile);
                $dbSaveSuccess = true;
                \Log::info("✅ User profile saved to Realtime DB");
            } catch (\Exception $dbError) {
                \Log::error("❌ Failed to save to Realtime DB: " . $dbError->getMessage());
                // Don't throw here - we still have the auth user
            }

            // Step 4: Verify the save
            if ($dbSaveSuccess) {
                \Log::info("📍 STEP 4: Verifying database save...");
                $verifyRef  = $this->database->getReference('users/' . $user->uid);
                $verifyData = $verifyRef->getValue();
                \Log::info("✅ Database verification: " . (! empty($verifyData) ? "SUCCESS" : "FAILED"));
            }

            \Log::info("\n🎉 ========== REGISTRATION SUCCESS ==========");
            \Log::info("📧 User: " . $user->email);
            \Log::info("🆔 UID: " . $user->uid);
            \Log::info("💾 Database save: " . ($dbSaveSuccess ? "SUCCESS" : "FAILED (but auth user created)"));

            return [
                'success'       => true,
                'user'          => [
                    'uid'         => $user->uid,
                    'email'       => $user->email,
                    'displayName' => $user->displayName,
                ],
                'dbSaveSuccess' => $dbSaveSuccess,
            ];

        } catch (AuthException $error) {
            \Log::error("\n❌ ========== REGISTRATION FAILED ==========");
            \Log::error("❌ Error code: " . $error->getCode());
            \Log::error("❌ Error message: " . $error->getMessage());

            $errorMessage = $error->getMessage();

            if (strpos($errorMessage, 'email-already-exists') !== false) {
                $errorMessage = "This email is already registered. Please login instead.";
            } elseif (strpos($errorMessage, 'invalid-email') !== false) {
                $errorMessage = "Invalid email address format.";
            } elseif (strpos($errorMessage, 'weak-password') !== false) {
                $errorMessage = "Password is too weak. Please use at least 6 characters.";
            }

            return ['success' => false, 'error' => $errorMessage];
        } catch (\Exception $error) {
            \Log::error("\n❌ ========== REGISTRATION FAILED ==========");
            \Log::error("❌ Error: " . $error->getMessage());
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }
    }

/**
 * Login user with better error handling and UI feedback
 */
    public function login($email, $password)
    {
        \Log::info("\n🔐 ========== LOGIN START ==========");
        \Log::info("📧 Email: " . $email);

        try {
            \Log::info("📍 STEP 1: Attempting Firebase Auth sign in...");

            // Try to sign in directly with Firebase Auth
            $signInResult = $this->auth->signInWithEmailAndPassword($email, $password);
            $user         = $signInResult->data();

            \Log::info("✅ Firebase Auth sign in successful");
            \Log::info("🆔 User UID: " . $user['localId']);

            // ✅ Check if user exists in our database (with fallback)
            \Log::info("📍 STEP 2: Checking user profile in Realtime DB...");
            $userProfile = $this->getUserProfile($user['localId']);

            if (empty($userProfile['data'])) {
                \Log::warning("⚠️ User authenticated but not in our database", ['uid' => $user['localId']]);
                return [
                    'success' => false,
                    'error'   => 'Account not properly registered. Please register first.',
                ];
            }

            \Log::info("✅ User profile verified in database");
            \Log::info("✅ LOGIN SUCCESS");

            return [
                'success' => true,
                'user'    => [
                    'uid'   => $user['localId'],
                    'email' => $user['email'],
                ],
            ];

        } catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $error) {
            \Log::error("\n❌ ========== LOGIN FAILED ==========");
            \Log::error("❌ Error: " . $error->getMessage());

            $errorMessage = $error->getMessage();

            // Better error message mapping for UI
            if (strpos($errorMessage, 'INVALID_LOGIN_CREDENTIALS') !== false ||
                strpos($errorMessage, 'EMAIL_NOT_FOUND') !== false ||
                strpos($errorMessage, 'INVALID_PASSWORD') !== false) {
                $errorMessage = "Invalid email or password. Please check your credentials.";
            } elseif (strpos($errorMessage, 'USER_NOT_FOUND') !== false) {
                $errorMessage = "No account found with this email. Please register first.";
            } elseif (strpos($errorMessage, 'TOO_MANY_ATTEMPTS_TRY_LATER') !== false) {
                $errorMessage = "Too many failed attempts. Please try again in a few minutes.";
            } elseif (strpos($errorMessage, 'USER_DISABLED') !== false) {
                $errorMessage = "This account has been disabled. Please contact support.";
            } else {
                $errorMessage = "Login failed. Please try again.";
            }

            return ['success' => false, 'error' => $errorMessage];
        } catch (\Exception $error) {
            \Log::error("\n❌ ========== LOGIN FAILED ==========");
            \Log::error("❌ Error: " . $error->getMessage());
            \Log::error("❌ Stack trace: " . $error->getTraceAsString());
            return ['success' => false, 'error' => 'An unexpected error occurred. Please try again.'];
        }
    }

    /**
     * Get user profile with fallback
     */
    public function getUserProfile($uid)
    {
        \Log::info("👤 [getUserProfile] Fetching profile for UID: " . $uid);
        try {
            $ref  = $this->database->getReference('users/' . $uid);
            $data = $ref->getValue();

            \Log::info("✅ [getUserProfile] Profile data: " . json_encode($data));

            return ['success' => true, 'data' => $data];
        } catch (\Exception $error) {
            \Log::error("❌ [getUserProfile] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage(), 'data' => null];
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile($uid, $userData)
    {
        \Log::info("✏️ [updateProfile] Updating profile for UID: " . $uid);
        \Log::info("📝 [updateProfile] New data: " . json_encode($userData));

        try {
            $userData['updatedAt'] = date('c');
            $this->database->getReference('users/' . $uid)->update($userData);

            \Log::info("✅ [updateProfile] Profile updated successfully");
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [updateProfile] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Logout user (Note: Server-side logout is different from client-side)
     */
    public function revokeUserTokens($uid)
    {
        \Log::info("👋 [revokeUserTokens] Revoking tokens for UID: " . $uid);
        try {
            $this->auth->revokeRefreshTokens($uid);
            \Log::info("✅ [revokeUserTokens] Tokens revoked successfully");
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [revokeUserTokens] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Get current user by UID
     */
    public function getUser($uid)
    {
        \Log::info("👤 [getUser] Getting user for UID: " . $uid);
        try {
            $user = $this->auth->getUser($uid);
            \Log::info("✅ [getUser] User retrieved: " . $user->email);

            return [
                'success' => true,
                'user'    => [
                    'uid'         => $user->uid,
                    'email'       => $user->email,
                    'displayName' => $user->displayName,
                ],
            ];
        } catch (\Exception $error) {
            \Log::error("❌ [getUser] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Verify ID token (for session management)
     */
    public function verifyIdToken($idToken)
    {
        try {
            $verifiedIdToken = $this->auth->verifyIdToken($idToken);
            $uid             = $verifiedIdToken->claims()->get('sub');

            return [
                'success' => true,
                'uid'     => $uid,
                'token'   => $verifiedIdToken,
            ];
        } catch (\Exception $error) {
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Database operations
     */
    public function create($path, $data)
    {
        \Log::info("💾 [DB create] Path: " . $path);
        try {
            $this->database->getReference($path)->set($data);
            \Log::info("✅ [DB create] Success");
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [DB create] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    public function read($path)
    {
        \Log::info("📖 [DB read] Path: " . $path);
        try {
            $data = $this->database->getReference($path)->getValue();
            \Log::info("✅ [DB read] Data retrieved");
            return ['success' => true, 'data' => $data];
        } catch (\Exception $error) {
            \Log::error("❌ [DB read] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    public function update($path, $data)
    {
        \Log::info("✏️ [DB update] Path: " . $path);
        try {
            $this->database->getReference($path)->update($data);
            \Log::info("✅ [DB update] Success");
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [DB update] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete($path)
    {
        \Log::info("🗑️ [DB delete] Path: " . $path);
        try {
            $this->database->getReference($path)->remove();
            \Log::info("✅ [DB delete] Success");
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [DB delete] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    public function query($path, $field, $value)
    {
        \Log::info("🔎 [DB query] Path: " . $path . " Field: " . $field . " Value: " . $value);
        try {
            $ref   = $this->database->getReference($path);
            $query = $ref->orderByChild($field)->equalTo($value);
            $data  = $query->getValue();

            \Log::info("✅ [DB query] Results: " . json_encode($data));
            return ['success' => true, 'data' => $data];
        } catch (\Exception $error) {
            \Log::error("❌ [DB query] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    public function getAuth()
    {
        return $this->auth;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function getWebsiteSettings($uid)
    {
        try {
            $ref      = $this->database->getReference("websites/{$uid}/settings");
            $settings = $ref->getValue();

            if (empty($settings)) {
                // Create default settings
                $defaultSettings = [
                    'colorPalette' => 'default',
                    'customColors' => [
                        'primary'   => '#000000',
                        'secondary' => '#8B4513',
                        'accent'    => '#FFFFFF',
                    ],
                ];
                $ref->set($defaultSettings);
                return $defaultSettings;
            }

            return $settings;
        } catch (\Exception $error) {
            \Log::error("❌ [getWebsiteSettings] Error: " . $error->getMessage());
            return null;
        }
    }

    /**
     * Update website settings
     */
    public function updateWebsiteSettings($uid, $settings)
    {
        try {
            $this->database->getReference("websites/{$uid}/settings")
                ->update($settings);
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [updateWebsiteSettings] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Get page content
     */
    /**
     * Get page content
     */
    public function getPageContent($uid, $page)
    {
        try {
            $ref     = $this->database->getReference("websites/{$uid}/pages/{$page}");
            $content = $ref->getValue();

            if (empty($content)) {
                // Create default page content
                $defaultContent = $this->getDefaultPageContent($page);
                $ref->set($defaultContent);
                return $defaultContent;
            }

            // Return content as-is
            // Adventures will be loaded separately by the controller
            return $content;
        } catch (\Exception $error) {
            \Log::error("❌ [getPageContent] Error: " . $error->getMessage());
            return null;
        }
    }

    /**
     * Update page content
     */
    public function updatePageContent($uid, $page, $content)
    {
        try {
            $this->database->getReference("websites/{$uid}/pages/{$page}")
                ->update($content);
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [updatePageContent] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Get analytics data
     */
    public function getAnalytics($uid)
    {
        try {
            $ref       = $this->database->getReference("analytics/{$uid}");
            $analytics = $ref->getValue();

            if (empty($analytics)) {
                // Create default analytics
                $defaultAnalytics = [
                    'pageViews' => ['home' => 0, 'about' => 0, 'contact' => 0],
                    'visitors'  => 0,
                    'sessions'  => 0,
                ];
                $ref->set($defaultAnalytics);
                return $defaultAnalytics;
            }

            return $analytics;
        } catch (\Exception $error) {
            \Log::error("❌ [getAnalytics] Error: " . $error->getMessage());
            return null;
        }
    }

    /**
     * Record page view
     */
    public function recordPageView($uid, $page)
    {
        try {
            $pageViewsRef = $this->database->getReference("analytics/{$uid}/pageViews/{$page}");
            $currentViews = $pageViewsRef->getValue() ?? 0;
            $pageViewsRef->set($currentViews + 1);

            // Also update total visitors/sessions (simplified)
            $visitorsRef     = $this->database->getReference("analytics/{$uid}/visitors");
            $currentVisitors = $visitorsRef->getValue() ?? 0;
            $visitorsRef->set($currentVisitors + 1);

            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [recordPageView] Error: " . $error->getMessage());
            return ['success' => false];
        }
    }

/**
 * Upload image to Publitio and save to Firebase
 */
    public function uploadGalleryImage($uid, $file, $imageData = [])
    {
        try {
            // Initialize Publitio service
            $publitioService = new \App\Services\PublitioService();

            // Upload to Publitio
            $uploadResult = $publitioService->uploadFile($file);

            if (! $uploadResult['success']) {
                \Log::error("❌ Publitio upload failed: " . $uploadResult['error']);
                return ['success' => false, 'error' => $uploadResult['error']];
            }

            // Prepare image data for Firebase
            $galleryImage = [
                'url'         => $uploadResult['url'],
                'publitio_id' => $uploadResult['publitio_id'],
                'caption'     => $imageData['caption'] ?? 'Adventure Photo',
                'location'    => $imageData['location'] ?? '',
                'uploaded_at' => date('c'),
                'bytes'       => $uploadResult['bytes'],
                'type'        => $uploadResult['type'],
            ];

            // Save to Firebase under user's gallery
            $galleryRef  = $this->database->getReference("websites/{$uid}/pages/gallery/images");
            $newImageRef = $galleryRef->push($galleryImage);

            \Log::info("✅ Gallery image uploaded and saved", [
                'user_id'      => $uid,
                'publitio_id'  => $uploadResult['publitio_id'],
                'firebase_key' => $newImageRef->getKey(),
            ]);

            return [
                'success'      => true,
                'image'        => $galleryImage,
                'firebase_key' => $newImageRef->getKey(),
            ];

        } catch (\Exception $error) {
            \Log::error("❌ Gallery image upload failed: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

/**
 * Delete gallery image from Publitio and Firebase
 */
    public function deleteGalleryImage($uid, $publitioId, $firebaseKey = null)
    {
        try {
            // Delete from Publitio
            $publitioService = new \App\Services\PublitioService();
            $deleteResult    = $publitioService->deleteFile($publitioId);

            if (! $deleteResult['success']) {
                \Log::error("❌ Publitio deletion failed: " . $deleteResult['error']);
                // Continue with Firebase deletion even if Publitio fails
            }

            // Delete from Firebase
            if ($firebaseKey) {
                // Delete using specific key
                $this->database->getReference("websites/{$uid}/pages/gallery/images/{$firebaseKey}")->remove();
            } else {
                // Find and delete by publitio_id
                $imagesRef = $this->database->getReference("websites/{$uid}/pages/gallery/images");
                $query     = $imagesRef->orderByChild('publitio_id')->equalTo($publitioId);
                $snapshot  = $query->getSnapshot();

                if ($snapshot->hasChildren()) {
                    foreach ($snapshot->getValue() as $key => $value) {
                        $this->database->getReference("websites/{$uid}/pages/gallery/images/{$key}")->remove();
                    }
                }
            }

            \Log::info("✅ Gallery image deleted", [
                'user_id'     => $uid,
                'publitio_id' => $publitioId,
            ]);

            return ['success' => true];

        } catch (\Exception $error) {
            \Log::error("❌ Gallery image deletion failed: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

/**
 * Get gallery images with proper structure
 */
    public function getGalleryImages($uid)
    {
        try {
            $ref    = $this->database->getReference("websites/{$uid}/pages/gallery/images");
            $images = $ref->getValue();

            if (empty($images)) {
                return [];
            }

            // Convert to array and ensure proper structure
            $galleryImages = [];
            foreach ($images as $key => $image) {
                $galleryImages[] = [
                    'id'          => $key,
                    'url'         => $image['url'] ?? '',
                    'publitio_id' => $image['publitio_id'] ?? '',
                    'caption'     => $image['caption'] ?? 'Adventure Photo',
                    'location'    => $image['location'] ?? '',
                    'uploaded_at' => $image['uploaded_at'] ?? date('c'),
                ];
            }

            return $galleryImages;

        } catch (\Exception $error) {
            \Log::error("❌ Get gallery images failed: " . $error->getMessage());
            return [];
        }
    }

/**
 * Update gallery image metadata
 */
    public function updateGalleryImage($uid, $imageId, $updates)
    {
        try {
            $this->database->getReference("websites/{$uid}/pages/gallery/images/{$imageId}")
                ->update($updates);

            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ Update gallery image failed: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

/**
 * Get default page structure matching wowlogbook.com style
 */
    private function getDefaultPageContent($page)
    {
        $defaults = [
            'home'    => [
                'title'     => 'My Adventure Log',
                'published' => true,
                'sections'  => [
                    'hero'     => [
                        'badge'           => 'Welcome to Your Adventure Log',
                        'title'           => 'Welcome to My Adventure Log',
                        'subtitle'        => 'Documenting my journeys and experiences',
                        'text'            => 'Start your adventure and share your stories with the world.',
                        'backgroundImage' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2074&q=80',
                        'cta1Title'       => 'Explore Adventures',
                        'cta1Subtitle'    => 'Start your journey today',
                        'cta2Title'       => 'We guide your path',
                        'cta2Subtitle'    => 'Get into action',
                        'stats'           => [
                            ['number' => '100+', 'label' => 'Adventures', 'description' => 'By our community'],
                            ['number' => '50+', 'label' => 'Countries', 'description' => 'Explored worldwide'],
                            ['number' => '1k+', 'label' => 'Photos', 'description' => 'Memories captured'],
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

            // In your getDefaultPageContent method, update the 'about' section:
            'about'   => [
                'title'     => 'About Our Adventure',
                'published' => true,
                'sections'  => [
                    'hero'         => [
                        'badge'            => 'Our Story & Mission',
                        'title'            => 'About UsS',
                        'highlightedTitle' => 'Adventure Journey',
                        'subtitle'         => 'Discover the story behind Adventure Log and the passionate team dedicated to helping you document and share your journeys with the world.',
                        'stats'            => [
                            [
                                'number'      => '5K+',
                                'label'       => 'Team Members',
                                'description' => 'Passionate Adventurers',
                            ],
                            [
                                'number'      => '50+',
                                'label'       => 'Countries Reached',
                                'description' => 'Global Community',
                            ],
                            [
                                'number'      => '3+',
                                'label'       => 'Years of Passion',
                                'description' => 'Dedicated Service',
                            ],
                        ],
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
                    'stats'        => [
                        'title'    => 'Adventure by the Numbers',
                        'subtitle' => 'Growing every day with passionate explorers',
                    ],
                    'values'       => [
                        'badge'    => 'WHAT DRIVES US',
                        'title'    => 'Our Core Values',
                        'subtitle' => 'The principles that guide everything we do',
                        'items'    => [
                            [
                                'title'       => 'Authenticity',
                                'description' => 'Real stories from real adventurers. We celebrate genuine experiences and authentic voices.',
                                'action'      => 'Stay True',
                            ],
                            [
                                'title'       => 'Inspiration',
                                'description' => 'Spark curiosity and adventure in others. Every story has the power to inspire someone\'s next journey.',
                                'action'      => 'Light the Way',
                            ],
                            [
                                'title'       => 'Community',
                                'description' => 'Connect with fellow explorers worldwide. Together, we create a global network of adventure seekers.',
                                'action'      => 'Grow Together',
                            ],
                        ],
                    ],
                    'cta'          => [
                        'title'      => 'Join Our Community',
                        'subtitle'   => 'Start documenting your adventures today and become part of a global community of explorers.',
                        'buttonText' => 'Get Started Free',
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
                'sections'  => [
                    'hero' => [
                        'title'            => 'Adventure Gallery',
                        'highlightedTitle' => 'Visual Stories',
                        'subtitle'         => 'Visual stories from incredible journeys around the world',
                        'badge'            => 'Welcome to Your Adventure Log',
                        'backgroundImage'  => 'https://plus.unsplash.com/premium_photo-1709371824843-2b72258fbd71?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1267',
                        'stat1Label'       => 'Photos Shared',
                        'stat2Label'       => 'Adventures Documented',
                        'stat3Label'       => 'Countries Covered',
                        'stat4Label'       => 'Community Active',
                        'countriesCovered' => 50,
                        'communityActive'  => '24/7',
                        'categories'       => [
                            'Mountain Expeditions',
                            'Forest Trails',
                            'Coastal Adventures',
                            'Desert Journeys',
                            'Urban Exploration',
                        ],
                        'cta1Title'        => 'Browse Gallery',
                        'cta1Subtitle'     => 'Explore stunning visuals',
                        'cta2Title'        => 'Share Your Story',
                        'cta2Subtitle'     => 'Upload your adventures',
                    ],
                ],
                'images'    => [],
            ],
            // In your FirebaseService.php, update the contact page defaults
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
                        'title'            => 'Get In Touch',
                        'subtitle'         => "We'd love to hear about your adventures and help you share them with the world",
                        'badge'            => "Let's Connect & Collaborate",
                        'highlightedTitle' => 'Start Your Journey',
                        'backgroundImage'  => 'https://images.unsplash.com/photo-1596524430615-b46475ddff6e?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=1170',
                        'emailTitle'       => 'Email Us',
                        'emailSubtitle'    => 'We reply within 1 hour',
                        'phoneTitle'       => 'Call Us',
                        'phoneNumber'      => '+1 (555) 123-4567',
                        'phoneSubtitle'    => 'Mon – Fri: 9AM – 6PM',
                        'locationTitle'    => 'Visit Us',
                        'addressLine1'     => '123 Adventure Lane',
                        'addressLine2'     => 'Los Angeles, CA',
                        'cta1Title'        => 'Send Message',
                        'cta1Subtitle'     => 'Get instant response',
                        'cta2Title'        => 'Schedule Call',
                        'cta2Subtitle'     => 'Book a meeting',
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

        return $defaults[$page] ?? ['title' => 'New Page', 'content' => 'Content coming soon...', 'published' => false];
    }

/**
 * Publish/unpublish page
 */
    public function togglePublishPage($uid, $page, $published)
    {
        try {
            $this->database->getReference("websites/{$uid}/pages/{$page}/published")
                ->set($published);
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [togglePublishPage] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Get available color palettes
     */
    public function getColorPalettes()
    {
        return [
            'default' => [
                'name'   => 'Classic',
                'colors' => [
                    'primary'     => '#000000',
                    'secondary'   => '#8B4513',
                    'accent'      => '#FFFFFF',
                    'header_text' => '#000000', // Dark text for light backgrounds
                    'header_bg'   => '#FFFFFF', // Light background
                ],
            ],
            'modern'  => [
                'name'   => 'Modern',
                'colors' => [
                    'primary'     => '#2D3748',
                    'secondary'   => '#4A5568',
                    'accent'      => '#F7FAFC',
                    'header_text' => '#2D3748', // Dark text
                    'header_bg'   => '#F7FAFC', // Light background
                ],
            ],
            'warm'    => [
                'name'   => 'Warm',
                'colors' => [
                    'primary'     => '#744210',
                    'secondary'   => '#B7791F',
                    'accent'      => '#FEF5E7',
                    'header_text' => '#744210', // Warm dark text
                    'header_bg'   => '#FEF5E7', // Warm light background
                ],
            ],
            'cool'    => [
                'name'   => 'Cool',
                'colors' => [
                    'primary'     => '#2C5282',
                    'secondary'   => '#4C7AA7',
                    'accent'      => '#EBF8FF',
                    'header_text' => '#2C5282', // Cool dark text
                    'header_bg'   => '#EBF8FF', // Cool light background
                ],
            ],
            'dark'    => [
                'name'   => 'Dark',
                'colors' => [
                    'primary'     => '#FFFFFF', // Light text for dark mode
                    'secondary'   => '#CBD5E0',
                    'accent'      => '#1A202C',
                    'header_text' => '#FFFFFF', // Light text
                    'header_bg'   => '#1A202C', // Dark background
                ],
            ],
        ];
    }

    /**
     * Get user-specific page content
     */
    public function getUserPageContent($uid, $page)
    {
        try {
            $ref     = $this->database->getReference("websites/{$uid}/pages/{$page}");
            $content = $ref->getValue();

            if (empty($content)) {
                // Create default page content for this user
                $defaultContent = $this->getDefaultPageContent($page);
                $ref->set($defaultContent);
                return $defaultContent;
            }

            return $content;
        } catch (\Exception $error) {
            \Log::error("❌ [getUserPageContent] Error: " . $error->getMessage());
            return $this->getDefaultPageContent($page);
        }
    }

    /**
     * Get user-specific recent adventures/posts
     */
    public function getUserAdventures($uid, $limit = 10)
    {
        try {
            $ref = $this->database->getReference("websites/{$uid}/adventures")
                ->orderByChild('createdAt')
                ->limitToLast($limit);

            $adventures = $ref->getValue();

            if (empty($adventures)) {
                return [];
            }

            // Convert to array and reverse to show latest first
            $adventuresArray = [];
            foreach ($adventures as $key => $adventure) {
                $adventuresArray[] = [
                    'id' => $key,
                    ...$adventure,
                ];
            }

            return array_reverse($adventuresArray);
        } catch (\Exception $error) {
            \Log::error("❌ [getUserAdventures] Error: " . $error->getMessage());
            return [];
        }
    }

    /**
     * Create a new adventure/post for user
     * ✅ FIXED: Save to the correct Firebase location
     */
    public function createAdventure($uid, $adventureData)
    {
        try {
            \Log::info("📝 [createAdventure] Creating adventure for UID: {$uid}");
            \Log::info("📝 [createAdventure] Adventure data: " . json_encode($adventureData));

            // Use the adventures collection (separate from pages)
            $adventureRef = $this->database->getReference("websites/{$uid}/adventures");

            $newAdventure = [
                'title'     => $adventureData['title'],
                'excerpt'   => $adventureData['excerpt'],
                'content'   => $adventureData['content'] ?? '',
                'image'     => $adventureData['image'],
                'date'      => $adventureData['date'] ?? date('Y-m-d'),
                'location'  => $adventureData['location'] ?? '',
                'tags'      => $adventureData['tags'] ?? [],
                'createdAt' => date('c'),
                'updatedAt' => date('c'),
                'published' => $adventureData['published'] ?? true,
            ];

            $newRef      = $adventureRef->push($newAdventure);
            $adventureId = $newRef->getKey();

            \Log::info("✅ [createAdventure] Adventure created successfully", [
                'id'    => $adventureId,
                'title' => $newAdventure['title'],
            ]);

            return [
                'success'   => true,
                'id'        => $adventureId,
                'adventure' => array_merge(['id' => $adventureId], $newAdventure),
            ];
        } catch (\Exception $error) {
            \Log::error("❌ [createAdventure] Error: " . $error->getMessage());
            \Log::error("❌ Stack trace: " . $error->getTraceAsString());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Migrate adventures from old location to new location
     * Call this once to move existing adventures
     */
    public function migrateAdventures($uid)
    {
        try {
            \Log::info("🔄 [migrateAdventures] Starting migration for UID: {$uid}");

            // Get adventures from old location (in pages/home/sections/recent/posts)
            $oldRef        = $this->database->getReference("websites/{$uid}/pages/home/sections/recent/posts");
            $oldAdventures = $oldRef->getValue();

            if (empty($oldAdventures)) {
                \Log::info("ℹ️ [migrateAdventures] No old adventures to migrate");
                return ['success' => true, 'migrated' => 0];
            }

            \Log::info("📊 [migrateAdventures] Found adventures to migrate", [
                'count' => count($oldAdventures),
            ]);

            // Get new location reference
            $newRef = $this->database->getReference("websites/{$uid}/adventures");

            $migratedCount = 0;

            // Convert array structure and migrate each adventure
            foreach ($oldAdventures as $index => $adventure) {
                // Skip if not a valid adventure object
                if (! is_array($adventure) || empty($adventure['title'])) {
                    continue;
                }

                // Prepare adventure data
                $adventureData = [
                    'title'     => $adventure['title'] ?? 'Untitled',
                    'excerpt'   => $adventure['excerpt'] ?? '',
                    'content'   => $adventure['content'] ?? '',
                    'image'     => $adventure['image'] ?? '',
                    'date'      => $adventure['date'] ?? date('Y-m-d'),
                    'location'  => $adventure['location'] ?? '',
                    'tags'      => $adventure['tags'] ?? [],
                    'createdAt' => $adventure['createdAt'] ?? date('c'),
                    'updatedAt' => date('c'),
                    'published' => $adventure['published'] ?? true,
                ];

                // Push to new location
                $newRef->push($adventureData);
                $migratedCount++;

                \Log::info("✅ [migrateAdventures] Migrated: {$adventure['title']}");
            }

            \Log::info("✅ [migrateAdventures] Migration complete", [
                'migrated' => $migratedCount,
            ]);

            return [
                'success'  => true,
                'migrated' => $migratedCount,
            ];

        } catch (\Exception $error) {
            \Log::error("❌ [migrateAdventures] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }
    /**
     * Update user adventure
     */
    public function updateAdventure($uid, $adventureId, $adventureData)
    {
        try {
            $adventureRef = $this->database->getReference("websites/{$uid}/adventures/{$adventureId}");

            $updateData = [
                 ...$adventureData,
                'updatedAt' => date('c'),
            ];

            $adventureRef->update($updateData);

            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [updateAdventure] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

    /**
     * Delete user adventure
     */
    public function deleteAdventure($uid, $adventureId)
    {
        try {
            $this->database->getReference("websites/{$uid}/adventures/{$adventureId}")->remove();
            return ['success' => true];
        } catch (\Exception $error) {
            \Log::error("❌ [deleteAdventure] Error: " . $error->getMessage());
            return ['success' => false, 'error' => $error->getMessage()];
        }
    }

}
