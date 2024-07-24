<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase, WithFaker;

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
  
    
    public function it_can_fetch_all_tasks_assigned_to_the_logged_in_user()
    {
        // Create users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create tasks and assign them to users
        $task1 = Task::factory()->create();
        $task1->users()->attach($user1->id);

        $task2 = Task::factory()->create();
        $task2->users()->attach($user2->id);

        // Act as user1
        $response = $this->actingAs($user1)->getJson('/api/v1/fetch-my-tasks');

        // Assert response contains the task assigned to user1
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'id' => $task1->id,
                     'title' => $task1->title,
                     'description' => $task1->description,
                 ])
                 ->assertJsonMissing([
                     'id' => $task2->id,
                 ]);
    }

    #[Test]
    public function only_creator_and_assigned_users_can_view_and_update_a_task()
    {
        $creator = User::factory()->create();
        $assignedUser = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);
        $task->users()->attach($assignedUser->id);

        // Test viewing by creator
        $response = $this->actingAs($creator)->getJson("/api/v1/task/{$task->task_unique_id}");
        $response->assertStatus(200);

        // Test viewing by assigned user
        $response = $this->actingAs($assignedUser)->getJson("/api/v1/task/{$task->task_unique_id}");
        $response->assertStatus(200);

        // Test viewing by other user
        $response = $this->actingAs($otherUser)->getJson("/api/v1/task/{$task->task_unique_id}");
        $response->assertStatus(403);

        // Test updating by creator
        $response = $this->actingAs($creator)->putJson("/api/v1/task/{$task->task_unique_id}", [
            'title' => 'Updated Title',
            'description' => "This is test description, please passsss",
            "status" => TaskStatus::COMPLETED->value
        ]);
        $response->assertStatus(200);

        // Test updating by assigned user
        $response = $this->actingAs($assignedUser)->putJson("/api/v1/task/{$task->task_unique_id}", [
            'title' => 'Updated Title',
            'description' => "This is test description, please passsss",
            "status" => TaskStatus::PENDING->value
        ]);
        $response->assertStatus(200);

        // Test updating by other user
        $response = $this->actingAs($otherUser)->putJson("/api/v1/task/{$task->task_unique_id}", [
            'title' => 'Updated Title',
            'description' => "This is test description, please passsss",
            "status" => TaskStatus::IN_PROGRESS->value
        ]);
        $response->assertStatus(403);
    }

    #[Test]
    public function test_only_creator_can_delete_a_task()
    {
        $creator = User::factory()->create();
        $assignedUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $creator->id]);
        $task->users()->attach($assignedUser->id);

        // Test deletion by creator
        $response = $this->actingAs($creator)->deleteJson("/api/v1/task/{$task->task_unique_id}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->task_unique_id]);

        // Re-create task for further tests
        $task = Task::factory()->create(['user_id' => $creator->id]);
        $task->users()->attach($assignedUser->id);

        // Test deletion by assigned user
        $response = $this->actingAs($assignedUser)->deleteJson("/api/v1/task/{$task->task_unique_id}");
        $response->assertStatus(403);

        // Test deletion by other user
        $otherUser = User::factory()->create();
        $response = $this->actingAs($otherUser)->deleteJson("/api/v1/task/{$task->task_unique_id}");
        $response->assertStatus(403);
    }

    #[Test]

    public function it_creates_a_task_successfully()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/task', [
            'title' => 'New Task Title',
            'description' => 'Task description here.',
            'status' => 'pending',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'priority' => 'medium',
            'tags' => ['tag1', 'tag2'],
            'attachments' => [],
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'title' => 'New Task Title',
                     'description' => 'Task description here.',
                 ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'New Task Title',
            'description' => 'Task description here.',
            'user_id' => $user->id,
        ]);
    }

    #[Test]

    public function user_who_did_not_create_or_is_not_assigned_to_task_cannot_view_or_update()
    {
        $creator = User::factory()->create();
        $assignedUser = User::factory()->create();
        $nonAssignedUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);
        $task->users()->attach($assignedUser->id);

        // Test viewing by non-assigned user
        $response = $this->actingAs($nonAssignedUser)->getJson("/api/v1/task/{$task->task_unique_id}");
        $response->assertStatus(403);

        // Test updating by non-assigned user
        $response = $this->actingAs($nonAssignedUser)->putJson("/api/v1/task/{$task->task_unique_id}", [
            'title' => 'Attempted Update',
            "description" => "nothing to describe, just pass",
            "status" => TaskStatus::PENDING->value
        ]);
        $response->assertStatus(403);
    }

}
