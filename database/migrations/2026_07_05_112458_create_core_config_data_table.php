<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('core_config_data', function (Blueprint $table) {
            $table->id('config_id');
            $table->string('scope', 8)->default('default'); // default, websites, stores
            $table->integer('scope_id')->default(0);
            $table->string('path', 255);
            $table->text('value')->nullable();
            $table->timestamps();

            // Đảm bảo không bị trùng lặp cấu hình
            $table->unique(['scope', 'scope_id', 'path']);
            $table->index('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_config_data');
    }
};
