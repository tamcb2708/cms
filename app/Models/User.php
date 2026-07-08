<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable(['name', 'email', 'username', 'password', 'phone', 'department', 'job_title', 'locale', 'cms_role_id', 'valid_from', 'valid_until', 'is_active', 'require_password_change'])]
#[Hidden(['password', 'remember_token', 'google2fa_secret', 'google2fa_recovery_codes'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public const MAX_LOGIN_ATTEMPTS = 5;

    public const LOCKOUT_MINUTES = 15;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'require_password_change' => 'boolean',
            'google2fa_enabled' => 'boolean',
            'google2fa_recovery_codes' => 'array',
            'lockout_until' => 'datetime',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_active;
    }

    public function cmsRole()
    {
        return $this->belongsTo(CmsRole::class, 'cms_role_id');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function isLocked(): bool
    {
        return $this->lockout_until !== null && $this->lockout_until->isFuture();
    }

    public function registerFailedLogin(): void
    {
        $this->increment('login_attempts');

        if ($this->login_attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $this->forceFill([
                'lockout_until' => now()->addMinutes(self::LOCKOUT_MINUTES),
            ])->save();
        }
    }

    public function resetLoginAttempts(): void
    {
        if ($this->login_attempts !== 0 || $this->lockout_until !== null) {
            $this->forceFill([
                'login_attempts' => 0,
                'lockout_until' => null,
            ])->save();
        }
    }

    /**
     * Whether this admin is allowed to perform $action (view/create/edit/delete/publish)
     * on the CMS module identified by $categorySlug.
     */
    public function hasModulePermission(string $categorySlug, string $action): bool
    {
        // The root system account predates the CMS role system and must
        // never be locked out (see AccountSettingsController::is_root_admin).
        if ($this->email === 'admin@eios.vn') {
            return true;
        }

        if (! $this->cms_role_id) {
            // Legacy accounts provisioned before the CMS role system only have
            // a Spatie role — keep Super Admins fully privileged.
            return $this->hasRole('Super Admin');
        }

        if ($this->cmsRole?->is_system && $this->cmsRole->id === 'administrator') {
            return true;
        }

        return CmsRolePermission::query()
            ->where('role_id', $this->cms_role_id)
            ->where('action', $action)
            ->where('is_allowed', true)
            ->whereHas('category', fn ($q) => $q->where('slug', $categorySlug))
            ->exists();
    }
}
