<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminDirectoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        $admins = User::query()
            ->with('cmsRole')
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->cmsRole?->name,
                'valid_from' => $user->valid_from,
                'valid_until' => $user->valid_until,
                'status' => $this->statusFor($user),
            ]);

        return Inertia::render('AdminDirectory/Index', [
            'admins' => $admins,
            'filters' => ['search' => $search],
        ]);
    }

    private function statusFor(User $user): string
    {
        $now = now();

        if ($user->valid_from && $now->lt($user->valid_from)) {
            return 'pending';
        }

        if ($user->valid_until && $now->gt($user->valid_until)) {
            return 'expired';
        }

        return 'active';
    }

    public function create(): Response
    {
        return Inertia::render('AdminDirectory/Create', [
            'roles' => $this->roles(),
        ]);
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

    public function edit(User $user): Response
    {
        return Inertia::render('AdminDirectory/Edit', [
            'admin' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->cms_role_id,
                'valid_from' => $user->valid_from,
                'valid_until' => $user->valid_until,
            ],
            'roles' => $this->roles(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:cms_roles,id',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
        ]);

        $user->fill([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'cms_role_id' => $request->role,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
        ]);

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin-directory.index')->with('success', 'Đã cập nhật quản trị viên.');
    }

    private function roles()
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('cms_roles')) {
            return [];
        }

        return \App\Models\CmsRole::all()->map(fn ($role) => [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'is_system' => $role->is_system,
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin-directory.index')->with('error', 'Không thể xoá tài khoản của chính bạn.');
        }

        $user->delete();

        return redirect()->route('admin-directory.index')->with('success', 'Đã xoá quản trị viên.');
    }
}
