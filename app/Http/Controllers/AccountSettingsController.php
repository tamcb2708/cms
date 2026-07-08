<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Services\AccountSettingsService;
use Inertia\Inertia;
use Inertia\Response;

class AccountSettingsController extends Controller
{
    private AccountSettingsService $accountSettingsService;

    public function __construct(AccountSettingsService $accountSettingsService)
    {
        $this->accountSettingsService = $accountSettingsService;
    }

    public function index(Request $request): Response
    {
        $user = Auth::user()->load('cmsRole');
        $tab = $request->query('tab', 'profile');

        return Inertia::render('Account/Index', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'phone' => $user->phone,
                'department' => $user->department,
                'job_title' => $user->job_title,
                'locale' => $user->locale,
                'valid_from' => $user->valid_from,
                'valid_until' => $user->valid_until,
                'cms_role' => $user->cmsRole ? ['name' => $user->cmsRole->name, 'is_system' => $user->cmsRole->is_system] : null,
                'is_root_admin' => $user->email === 'admin@eios.vn',
            ],
            'tab' => $tab,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        if ($user->email !== 'admin@eios.vn') {
            $rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
            $rules['phone'] = ['nullable', 'string', 'max:20'];
            $rules['job_title'] = ['nullable', 'string', 'max:100'];
            $rules['department'] = ['nullable', 'string', 'max:100'];
            $rules['locale'] = ['nullable', 'string', Rule::in(['en', 'vi'])];
        }

        $validated = $request->validate($rules);

        $this->accountSettingsService->updateProfile($user, $validated);

        return back()->with('success_profile', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($user->email === 'admin@eios.vn') {
            return back()->withErrors(['current_password' => 'Tài khoản Root System không được phép đổi mật khẩu.']);
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $this->accountSettingsService->updatePassword($request->user(), $validated['password']);

        return back()->with('success_password', 'Mật khẩu đã được thay đổi thành công!');
    }

    public function updateLocale(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(['en', 'vi'])],
        ]);

        $this->accountSettingsService->updateLocale($request->user(), $validated['locale']);

        return back();
    }
}
