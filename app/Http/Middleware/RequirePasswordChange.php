<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     * Redirect to change password page if user's password equals username
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if password equals username (unhashed password)
            // We check by attempting to login with username as password
            // But a simpler way: check if Hash::check(username, password) is true
            if (Hash::check($user->name, $user->password)) {
                // Allow access to change password route and logout, block everything else
                if (!$request->routeIs('change-password') && !$request->routeIs('change-password-post') && !$request->routeIs('log-out') && !$request->routeIs('login')) {
                    return redirect()->route('change-password')->with('warning', 'Vui lòng đổi mật khẩu của bạn để tiếp tục sử dụng hệ thống.');
                }
            }
        }

        return $next($request);
    }
}
