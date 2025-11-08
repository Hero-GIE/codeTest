<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class RegisterController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function showRegistrationForm()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        \Log::info("🚀 Registration attempt", ['email' => $request->email]);

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            \Log::warning("❌ Registration validation failed", $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // ✅ Check if user already exists in our database
            $userExists = $this->firebaseService->checkUserExists($request->email);

            if ($userExists) {
                \Log::warning("❌ User already exists", ['email' => $request->email]);
                return redirect()->back()
                    ->with('error', 'An account with this email already exists. Please login instead.')
                    ->withInput();
            }

            // Register new user
            $result = $this->firebaseService->register(
                $request->email,
                $request->password,
                ['name' => $request->name]
            );

            if ($result['success']) {
                \Log::info("✅ Registration successful", ['user_id' => $result['user']['uid']]);

                return redirect('/login')
                    ->with('success', 'Registration successful! Please login with your credentials.');
            } else {
                \Log::error("❌ Registration failed", ['error' => $result['error']]);

                return redirect()->back()
                    ->with('error', $result['error'] ?? 'Registration failed. Please try again.')
                    ->withInput();
            }
        } catch (\Exception $e) {
            \Log::error("❌ Registration exception", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Registration failed. Please try again.')
                ->withInput();
        }
    }
}
