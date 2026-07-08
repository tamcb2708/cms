<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * These columns already exist on some deployments (added directly to the
 * database). This migration formalizes them for fresh installs, guarded by
 * hasColumn so it's a no-op where they're already present.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'require_password_change')) {
                $table->boolean('require_password_change')->default(true)->after('is_active');
            }
            if (! Schema::hasColumn('users', 'google2fa_secret')) {
                $table->text('google2fa_secret')->nullable()->after('require_password_change');
            }
            if (! Schema::hasColumn('users', 'google2fa_enabled')) {
                $table->boolean('google2fa_enabled')->default(false)->after('google2fa_secret');
            }
            if (! Schema::hasColumn('users', 'google2fa_recovery_codes')) {
                $table->json('google2fa_recovery_codes')->nullable()->after('google2fa_enabled');
            }
            if (! Schema::hasColumn('users', 'login_attempts')) {
                $table->unsignedInteger('login_attempts')->default(0)->after('google2fa_recovery_codes');
            }
            if (! Schema::hasColumn('users', 'lockout_until')) {
                $table->timestamp('lockout_until')->nullable()->after('login_attempts');
            }
        });

        if (! Schema::hasTable('login_histories')) {
            Schema::create('login_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('status', 20);
                $table->text('location')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'require_password_change',
                'google2fa_secret',
                'google2fa_enabled',
                'google2fa_recovery_codes',
                'login_attempts',
                'lockout_until',
            ]);
        });

        Schema::dropIfExists('login_histories');
    }
};
