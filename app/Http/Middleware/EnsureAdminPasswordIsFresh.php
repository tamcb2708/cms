<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Forces admins flagged with require_password_change to update their
 * password before they can use anything else in the CMS or Filament panel.
 */
class EnsureAdminPasswordIsFresh
{
    private const ALLOWED_ROUTES = [
        'account.settings',
        'account.password',
        'dashboard.logout',
        'filament.admin.auth.logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($request->is('livewire/*')) {
            return $next($request);
        }

        if ($user && $user->require_password_change && ! $request->routeIs(self::ALLOWED_ROUTES)) {
            return redirect()->route('account.settings', ['tab' => 'password'])
                ->with('error', 'Bạn phải đổi mật khẩu trước khi tiếp tục sử dụng hệ thống.');
        }

        return $next($request);
    }
}
