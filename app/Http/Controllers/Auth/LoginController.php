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
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Use your FirebaseService to handle authentication
        $result = $this->firebaseService->login(
            $request->email,
            $request->password
        );

        if ($result['success']) {
            // Store user in session
            $request->session()->put('firebase_user', $result['user']);
            $request->session()->put('firebase_authenticated', true);

            return redirect()->intended('/dashboard')
                ->with('success', 'Login successful!');
        }

        return redirect()->back()
            ->with('error', $result['error'])
            ->withInput();
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
