<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'cms_role_id')) {
                $table->string('cms_role_id')->nullable()->after('password');
                $table->foreign('cms_role_id')->references('id')->on('cms_roles')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cms_role_id']);
            $table->dropColumn('cms_role_id');
        });
    }
};
