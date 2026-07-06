<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountSettingsService
{
    public function updateProfile(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function updatePassword(User $user, string $newPassword): bool
    {
        return $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    public function updateLocale(User $user, string $locale): bool
    {
        return $user->update(['locale' => $locale]);
    }
}
