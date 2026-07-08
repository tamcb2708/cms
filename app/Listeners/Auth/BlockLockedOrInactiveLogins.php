<?php

namespace App\Listeners\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Validation\ValidationException;

/**
 * Runs before credential validation so a correct password still can't log
 * a locked-out or deactivated admin in.
 */
class BlockLockedOrInactiveLogins
{
    public function handle(Attempting $event): void
    {
        $email = $event->credentials['email'] ?? null;

        if (! $email) {
            return;
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return;
        }

        if ($user->isLocked()) {
            $minutes = now()->diffInMinutes($user->lockout_until) ?: 1;

            throw ValidationException::withMessages([
                'email' => "Tài khoản đã bị khoá tạm thời do đăng nhập sai nhiều lần. Vui lòng thử lại sau {$minutes} phút.",
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => 'Tài khoản của bạn đã bị vô hiệu hoá.',
            ]);
        }
    }
}
