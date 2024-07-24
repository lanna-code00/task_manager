<?php

namespace Database\Factories;

use App\Enums\PriorityStatus;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Utils\HtmlSanitize;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement([TaskStatus::COMPLETED->value, TaskStatus::IN_PROGRESS->value, TaskStatus::PENDING->value]);
        $priority = $this->faker->randomElement([PriorityStatus::LOW->value, PriorityStatus::MEDIUM->value, PriorityStatus::URGENT->value, PriorityStatus::HIGH->value]);

        return [
            'title' => $this->faker->sentence(3), 
            'description' => HtmlSanitize::sanitizeHtml(Str::words($this->faker->text, 100)), 
            'status' => $status, 
            'start_date' => $this->faker->dateTimeBetween('-1 month', 'now'), 
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'), 
            'priority' => $priority, 
            'tags' => $this->faker->words(3, true),
            'attachments' => [
                'file1.jpg',
                'file2.pdf',
            ], 
            'completion_date' => $this->faker->optional()->dateTimeBetween('start_date', 'now'), 
            'meta' => ['extra_info' => 'Some additional information'],
            'user_id' => User::factory(),
        ];
    }

    public function forUser(User $user): Factory
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }

    public function assignToRandomUsers(int $count = 3): Factory
    {
        return $this->afterCreating(function (Task $task) use ($count) {
            $users = User::inRandomOrder()->limit($count)->get();
            $task->users()->attach($users->pluck('id')->toArray());
        });
    }
}
