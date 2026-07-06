<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAccountValidity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $now = now();

            $isInvalid = false;
            if ($user->valid_from && $now->lt($user->valid_from)) {
                $isInvalid = true;
            }
            if ($user->valid_until && $now->gt($user->valid_until)) {
                $isInvalid = true;
            }

            if ($isInvalid) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->withErrors([
                    'email' => 'Tài khoản của bạn đã hết hạn hoặc chưa có hiệu lực truy cập.',
                ]);
            }
        }

        return $next($request);
    }
}
