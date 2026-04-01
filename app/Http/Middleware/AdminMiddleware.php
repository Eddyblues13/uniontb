<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->to('/'); // Redirects to user/home URL
        }

        // Allow access if the user is impersonating (original admin session exists)
        if (session()->has('impersonate')) {
            return $next($request);
        }

        // Check if the user is authenticated as an admin
        if (Auth::user()->user_type !== 'admin') {
            return redirect('/')->with('error', 'You do not have admin access.');
        }
        return $next($request);
    }
}
