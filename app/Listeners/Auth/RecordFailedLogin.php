<?php

namespace App\Listeners\Auth;

use App\Models\LoginHistory;
use App\Models\User;
use App\Notifications\AdminAccountLockedNotification;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class RecordFailedLogin
{
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email'] ?? null;
        $user = $event->user ?? ($email ? User::where('email', $email)->first() : null);

        if (! $user) {
            return;
        }

        $user->registerFailedLogin();

        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => $user->isLocked() ? 'locked' : 'failed',
            'created_at' => now(),
        ]);

        if ($user->isLocked() && $user->login_attempts == User::MAX_LOGIN_ATTEMPTS) {
            $this->notifySuperAdmins($user);
        }
    }

    private function notifySuperAdmins(User $user): void
    {
        $superAdmins = User::where('cms_role_id', 'administrator')
            ->orWhereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))
            ->get();

        if ($superAdmins->isEmpty()) {
            return;
        }

        try {
            Notification::send($superAdmins, new AdminAccountLockedNotification($user, request()->ip()));
        } catch (\Throwable $e) {
            Log::warning('Failed to send account-locked notification: '.$e->getMessage());
        }
    }
}
