<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EnsureCmsPermission
{
    public function handle(Request $request, Closure $next, string $categorySlug, string $action)
    {
        $user = Auth::user();

        if (! $user || ! $user->hasModulePermission($categorySlug, $action)) {
            throw new HttpException(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        return $next($request);
    }
}
