<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Utils\HtmlSanitize;
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
        return [
            'title' => $this->faker->sentence(3),
            'description' => HtmlSanitize::sanitizeHtml($this->faker->randomHtml()),
            'status' => $this->faker->randomElement([TaskStatus::COMPLETED->value, TaskStatus::IN_PROGRESS->value, TaskStatus::PENDING->value]),
        ];
    }
}
