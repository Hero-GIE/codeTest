<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class LoginController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        \Log::info("🔐 Login attempt", ['email' => $request->email]);

        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            \Log::warning("❌ Validation failed", $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check your credentials and try again.');
        }

        try {
            // ✅ Check if user exists in our database (with fallback handling)
            \Log::info("📍 Checking if user exists in database...");
            $userExists = $this->firebaseService->checkUserExists($request->email);

            if (! $userExists) {
                \Log::warning("❌ User not found in database", ['email' => $request->email]);
                return redirect()->back()
                    ->with('error', 'No account found with this email. Please register first.')
                    ->withInput();
            }

            \Log::info("✅ User exists in database, proceeding with authentication...");

            // Use your FirebaseService to handle authentication
            $result = $this->firebaseService->login(
                $request->email,
                $request->password
            );

            \Log::info("📊 Login result", ['success' => $result['success']]);

            if ($result['success']) {
                // ✅ Verify user exists in database before storing in session
                $userProfile = $this->firebaseService->getUserProfile($result['user']['uid']);

                if (empty($userProfile['data'])) {
                    \Log::error("❌ User authenticated but profile missing", ['uid' => $result['user']['uid']]);
                    return redirect()->back()
                        ->with('error', 'Account configuration error. Please contact support.')
                        ->withInput();
                }

                // Store user in session
                $request->session()->put('firebase_user', $result['user']);
                $request->session()->put('firebase_authenticated', true);

                \Log::info("✅ Login successful", ['user_id' => $result['user']['uid']]);

                // ✅ Ensure redirect to dashboard
                return redirect()->intended('/dashboard')
                    ->with('success', 'Login successful! Welcome back!');
            }

            \Log::warning("❌ Login failed", ['error' => $result['error'] ?? 'Unknown error']);

            return redirect()->back()
                ->with('error', $result['error'] ?? 'Login failed. Please try again.')
                ->withInput();

        } catch (\Exception $e) {
            \Log::error("❌ Login exception", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'An unexpected error occurred. Please try again.')
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        $user = $request->session()->get('firebase_user');

        if ($user) {
            // Revoke tokens in Firebase
            $this->firebaseService->revokeUserTokens($user['uid']);
        }

        // Clear session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Logged out successfully!');
    }
}
