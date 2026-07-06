<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('cms_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('module');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('level')->default(1);
            $table->timestamps();
        });

        Schema::create('cms_category_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('cms_categories')->onDelete('cascade');
            $table->string('setting_key');
            $table->text('setting_value')->nullable();
            $table->timestamps();
            
            $table->unique(['category_id', 'setting_key']);
        });

        Schema::create('cms_roles', function (Blueprint $table) {
            $table->string('id')->primary(); // Using string for easy readable IDs like 'administrator', 'content_editor'
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false); // If true, cannot be deleted
            $table->timestamps();
        });

        Schema::create('cms_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role_id');
            $table->foreign('role_id')->references('id')->on('cms_roles')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('cms_categories')->onDelete('cascade');
            $table->string('action'); // e.g., 'view', 'create', 'edit', 'delete', 'publish'
            $table->boolean('is_allowed')->default(true);
            $table->timestamps();
            
            $table->unique(['role_id', 'category_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_role_permissions');
        Schema::dropIfExists('cms_roles');
        Schema::dropIfExists('cms_category_settings');
        Schema::dropIfExists('cms_categories');
    }
};
