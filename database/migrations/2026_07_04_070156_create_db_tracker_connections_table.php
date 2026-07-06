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
        Schema::create('db_tracker_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('tab');
            $table->string('host');
            $table->integer('port')->default(5432);
            $table->string('dbname');
            $table->string('user');
            $table->text('password');
            $table->timestamps();

            $table->unique(['user_id', 'tab']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('db_tracker_connections');
    }
};
