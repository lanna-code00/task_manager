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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->index();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('status', [TaskStatus::COMPLETED->value, TaskStatus::IN_PROGRESS->value, TaskStatus::PENDING->value])->default(TaskStatus::PENDING->value); // Task status
            $table->dateTime('start_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('priority')->nullable();
            $table->json('tags')->nullable();
            $table->json('attachments')->nullable();
            $table->dateTime('completion_date')->nullable();
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
