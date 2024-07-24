<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use DatabaseTransactions, WithFaker;

    protected User $user;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }


    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }

    public function test_can_create_task()
    {

        $taskData = [

            'title' => 'Test Task Title',

            'description' => 'Test Task Description',

            'status' => TaskStatus::PENDING->value,

        ];

        $response = $this->postJson('/api/v1/task', $taskData);

        $response->assertStatus(201)

                 ->assertJson([

                     'status' => 'success',

                     'message' => 'Task created successfully',

                 ]);

        $task = Task::where('title', 'Test Task Title')->where('user_id', $this->user->id)->first();

        $this->assertNotNull($task);

        $this->assertDatabaseHas('tasks', array_merge($taskData, ['id' => $task->id]));
        
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [

            'title' => 'Updated Task Title',

            'description' => 'Updated Task Description',

            'status' => TaskStatus::COMPLETED->value,
        ];

        $response = $this->putJson("/api/v1/task/{$task->task_unique_id}", $updatedData);

        $response->assertStatus(200)

                 ->assertJson([

                     'status' => 'success',

                     'message' => 'Task updated successfully',

                 ]);

        $this->assertDatabaseHas('tasks', array_merge($updatedData, ['id' => $task->id]));
    }

    public function test_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/v1/task/{$task->task_unique_id}");

        $response->assertStatus(200)
        
                 ->assertJson([

                     'status' => 'success',

                     'message' => 'Task deleted successfully',

                 ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_cannot_update_task_belonging_to_another_user()
    {

        $otherUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $updatedData = [

            'title' => 'Unauthorized Update',

            'description' => 'This should not be updated',

            'status' => TaskStatus::IN_PROGRESS->value,

        ];

        $response = $this->putJson("/api/v1/task/{$task->task_unique_id}", $updatedData);

        $response->assertStatus(403)

                 ->assertJson([

                     'status' => 'error',

                     'message' => 'Unauthorized',

                 ]);
    }
}
