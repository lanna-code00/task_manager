<?php

namespace Tests\Feature;

use App\Enums\PriorityStatus;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;


// I HAD TO USE SANCTUM FACADE FOR AUTHENTICATION , I DON'T KNOW IF THERE'S ANOTHER APPROACH
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

        $app = $this->createApplication();

        $app->loadEnvironmentFrom('.env.testing');
        
        $this->user = User::factory()->create();
        
        $this->actingAs($this->user);

    }
    public function testDatabaseConnection() //trying to test my connection to test database
    {
        $databaseName = \DB::connection()->getDatabaseName();

        $this->assertEquals('task_manager_test', $databaseName);
    }
  
    #[Test]
    public function it_can_fetch_all_tasks_assigned_to_the_logged_in_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
    
        // assign tasks to users
        $task1 = Task::factory()->create(['user_id' => $user1->id]); 
        $task1->users()->attach($user1->id);
    
        $task2 = Task::factory()->create(['user_id' => $user2->id]); 
        $task2->users()->attach($user2->id);
    
        Sanctum::actingAs($user1);
    
        $response = $this->getJson('/api/v1/fetch-my-tasks');
    
        // \Log::info('response', ['response' => $response->json()]);
    
        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'status',
                    'data' => [
                        'data' => [
                            '*' => [
                                'task_unique_id',
                                'title',
                                'description',
                                'status',
                                'due_date',
                            ]
                        ]
                    ]
                 ])
                 ->assertJsonFragment([
                     'task_unique_id' => $task1->task_unique_id,
                 ])
                 ->assertJsonMissing([
                     'task_unique_id' => $task2->task_unique_id,
                 ]);
    }

    public function test_creator_can_view_the_task()
    {
        $creator = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);
    
        Sanctum::actingAs($creator, ['*']);

        $response = $this->getJson("/api/v1/task/{$task->task_unique_id}");
    
        $response->assertStatus(200);
    
        // checking if the json response contains the expected keys
        $response->assertJsonStructure([

            'status',

            'data' => 
            
            [
                'task_unique_id','title','description','status',

                'assigned_users' => [
                    '*' => [  // the wildcard is for any element in the array at all
                        'name',
                        'unique_id',
                        'email',
                        'created_at',
                    ]
                ]
            ]
        ]);
    }    

    public function test_assigned_user_can_view_the_task()
    {

        $creator = User::factory()->create();

        $assignedUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);

        $task->users()->attach($assignedUser->id);

        Sanctum::actingAs($assignedUser, ['*']);

        $response = $this->getJson("/api/v1/task/{$task->task_unique_id}");

        $response->assertStatus(200);

        $response->assertJsonStructure([

            'status',

            'data' => [
                
                'task_unique_id','title','description','status',
                
                'assigned_users' => [
                    '*' => [  
                        'name',
                        'unique_id',
                        'email',
                        'created_at',
                    ]
                ]
            ]
        ]);
    }

    public function test_other_user_cannot_view_the_task()
    {
        $creator = User::factory()->create();

        $otherUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);


        Sanctum::actingAs($otherUser, ['*']);

        $response = $this->getJson("/api/v1/task/{$task->task_unique_id}");

        $response->assertStatus(403);

    }

    public function test_creator_can_update_the_task()
    {

        $creator = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);

        $updateData = [

            'title' => 'Updated Title by Creator',

            'description' => 'Updated description by creator',

            'status' => 'completed',

        ];

        Sanctum::actingAs($creator, ['*']);

        $response = $this->putJson("/api/v1/task/{$task->task_unique_id}", $updateData);

        $response->assertStatus(200)

                ->assertJson([

                    'status' => 'success',

                    'data' => array_merge(['task_unique_id' => $task->task_unique_id], $updateData)

                ]);

    }

    public function test_assigned_user_can_update_the_task()
    {

        $creator = User::factory()->create();

        $assignedUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);

        $task->users()->attach($assignedUser->id);

        $updateData = [

            'title' => 'Updated Title by Assigned User',

            'description' => 'Updated description by assigned user',

            'status' => 'pending',

        ];

        Sanctum::actingAs($assignedUser, ['*']);

        $response = $this->putJson("/api/v1/task/{$task->task_unique_id}", $updateData);

        // checks if the res status 200 and if the task is also updated
        $response->assertStatus(200)

                ->assertJson([

                    'status' => 'success',

                    'data' => array_merge(['task_unique_id' => $task->task_unique_id], $updateData)

                ]);


    }

    public function test_other_user_cannot_update_the_task()
    {

        $creator = User::factory()->create();

        $otherUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);


        $updateData = [

            'title' => 'Updated Title by Other User',

            'description' => 'Updated description by other user',

            'status' => 'in_progress',

        ];

        Sanctum::actingAs($otherUser, ['*']);

        $response = $this->putJson("/api/v1/task/{$task->task_unique_id}", $updateData);

        $response->assertStatus(403);

    }

    #[Test]
    public function only_creator_of_the_task_can_delete_a_it()
    {

        $creator = User::factory()->create();

        $assignedUser = User::factory()->create();

        $otherUser = User::factory()->create();


        // Create a task and assign it to the creator
        $task = Task::factory()->create(['user_id' => $creator->id]);

        $task->users()->attach($assignedUser->id);

        //  deleting the task as the creator
        Sanctum::actingAs($creator, ['*']);

        $response = $this->deleteJson("/api/v1/task/{$task->task_unique_id}");

        $response->assertStatus(200);

        //checcks if the task has been deleted
        $this->assertDatabaseMissing('tasks', ['task_unique_id' => $task->task_unique_id]);

        // create task again for further tests
        $task = Task::factory()->create(['user_id' => $creator->id]);

        $task->users()->attach($assignedUser->id);


        // try as an assigned user and try to delete the task
        Sanctum::actingAs($assignedUser, ['*']);

        $response = $this->deleteJson("/api/v1/task/{$task->task_unique_id}");

        $response->assertStatus(403);

        // try as another user and try to delete the task
        Sanctum::actingAs($otherUser, ['*']);

        $response = $this->deleteJson("/api/v1/task/{$task->task_unique_id}");

        $response->assertStatus(403);

    }

    #[Test]

    public function test_creating_a_task_successfully()
    {

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/task', [

            'title' => 'New Task Title',

            'description' => 'Task description here.',

            'status' => TaskStatus::PENDING->value,

            'start_date' => now()->toDateString(),

            'due_date' => now()->addDays(7)->toDateString(),

            'priority' => PriorityStatus::MEDIUM->value,

            'tags' => ['tag1', 'tag2'],

        ]);

        $response->assertStatus(201)

                 ->assertJsonFragment([

                     'title' => 'New Task Title',

                     'description' => 'Task description here.',

                     'status' => TaskStatus::PENDING->value,

                 ]);

        $this->assertDatabaseHas('tasks', [

            'title' => 'New Task Title',

            'description' => 'Task description here.',

            'status' => TaskStatus::PENDING->value,

            'user_id' => $user->id,

        ]);

    }

    #[Test]

    public function user_who_did_not_create_task_or_is_not_assigned_to_an_task_cannot_view_or_update()
    {

        $creator = User::factory()->create();

        $assignedUser = User::factory()->create();

        $nonAssignedUser = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $creator->id]);

        $task->users()->attach($assignedUser->id);

        // trying to vieww a task by non-assigned user
        $response = $this->actingAs($nonAssignedUser)->getJson("/api/v1/task/{$task->task_unique_id}");

        $response->assertStatus(403);

        // trying to test updating a task by non-assigned user
        $response = $this->actingAs($nonAssignedUser)->putJson("/api/v1/task/{$task->task_unique_id}", [

            'title' => 'Attempted Update',

            "description" => "nothing to describe, just pass",

            "status" => TaskStatus::PENDING->value

        ]);

        $response->assertStatus(403);

    }
    
}
