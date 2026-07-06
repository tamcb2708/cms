<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Check if user has an admin role (optional, but good practice for CMS)
            if (!$user->hasAnyRole(['Super Admin', 'Tenant Admin'])) {
                Auth::logout();
                return response()->json(['message' => 'Unauthorized. Not an admin.'], 403);
            }

            $token = $user->createToken('admin-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name')
                ],
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => $user,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
