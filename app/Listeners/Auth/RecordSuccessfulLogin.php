<?php

namespace App\Listeners\Auth;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;

class RecordSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        $user->resetLoginAttempts();

        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'success',
            'created_at' => now(),
        ]);
    }
}
