<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        // Redirect to dashboard if already authenticated
        if (session()->has('admin_authenticated')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        // Validate the request
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        // Check credentials against config
        $adminEmail = config('cashier.admin.email');
        $adminPassword = config('cashier.admin.password');
        $adminName = config('cashier.admin.name');

        if ($email === $adminEmail && $password === $adminPassword) {
            // Set session variables
            session([
                'admin_authenticated' => true,
                'admin_email' => $email,
                'admin_name' => $adminName,
            ]);

            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back, ' . $adminName . '!');
        }

        // Authentication failed
        return back()
            ->withErrors(['email' => 'Invalid credentials.'])
            ->onlyInput('email');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Clear all session data
        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
}
