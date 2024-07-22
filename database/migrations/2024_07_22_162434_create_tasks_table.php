<?php

use App\Enums\TaskStatus;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_unique_id');
            $table->foreignId('user_id')->constrained('users')->index();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('status', [TaskStatus::COMPLETED->value, TaskStatus::IN_PROGRESS->value, TaskStatus::PENDING->value])->default(TaskStatus::PENDING->value);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
