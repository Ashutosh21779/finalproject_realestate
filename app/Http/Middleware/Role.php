<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Check if the user has the required role
        if($request->user()->role !== $role ){
            // Log the redirect for debugging
            \Log::info('Role middleware redirecting user ID: ' . $request->user()->id .
                       ' with role: ' . $request->user()->role .
                       ' from path: ' . $request->path() .
                       ' to dashboard (required role: ' . $role . ')');

            // Redirect to the appropriate dashboard based on user role
            if ($request->user()->role === 'admin') {
                return redirect('admin/dashboard');
            } elseif ($request->user()->role === 'agent') {
                return redirect('agent/dashboard');
            } else {
                return redirect('dashboard');
            }
        }

        return $next($request);
    }
}
