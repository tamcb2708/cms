<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDirectoryController extends Controller
{
    public function index()
    {
        return view('pages.placeholder', ['title' => __('messages.admin_list')]);
    }

    public function create()
    {
        $hasTables = \Illuminate\Support\Facades\Schema::hasTable('cms_roles');
        $roles = [];
        if ($hasTables) {
            $roles = \App\Models\CmsRole::all();
        }

        return view('pages.admin-directory.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Validation logic here
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:cms_roles,id',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'cms_role_id' => $request->role,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        return redirect()->route('admin-directory.index')->with('success', __('messages.admin_created_success') ?? 'Admin user created successfully.');
    }
}
