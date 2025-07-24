<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show the registration form
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Optionally log in the user after registration
        auth()->login($user);

        // Redirect to a desired route (for example, the home page)
        return redirect()->route('user.login')->with('success', 'Registration successful!');
    }

    // Show the login form
    public function showLoginForm()
    {
        return view('auth.register');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('home')->with('success', 'Login successful!');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('home')->with('success', 'Logout successful!');
    }
}
