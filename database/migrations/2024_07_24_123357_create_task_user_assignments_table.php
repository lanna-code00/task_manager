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
        Schema::create('task_user_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('task_id')->constrained('tasks')->name('fk_task_id')->index();
        $table->foreignId('user_id')->constrained('users')->name('fk_user_id')->index();
        $table->json('meta')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_user_assignments');
    }
};
