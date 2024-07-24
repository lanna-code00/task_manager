<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
// use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '12345678',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure()
    {

        return $this->afterCreating(function (User $user) {

            $tasks = Task::factory()->count(4)->create([
            
                'user_id' => $user->id,
            
            ]);

            // Assign tasks to other users
            $tasks->each(function (Task $task) use ($user) {

                $assignedUsers = User::inRandomOrder()->limit(3)->get(); // fetch random users to assign tasks to
                
                $task->users()->attach($assignedUsers->pluck('id')->toArray());
            
            });
        });
    }
}
