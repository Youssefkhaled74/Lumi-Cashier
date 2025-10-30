<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if admin is authenticated via session
        if (!session()->has('admin_authenticated')) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this area.');
        }

        // Check if the session admin email matches config
        if (session('admin_email') !== config('cashier.admin.email')) {
            session()->flush();
            return redirect()->route('login')
                ->with('error', 'Invalid session. Please login again.');
        }

        return $next($request);
    }
}
