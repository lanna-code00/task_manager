<?php

namespace Tests\Unit;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
// use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    protected User $user;
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {

        $this->assertTrue(true);

    }

    protected function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()->create(); 

    }

    public function test_task_belongs_to_user()
    {

        $task = Task::factory()->forUser($this->user)->create();

        $this->assertInstanceOf(User::class, $task->user);

        $this->assertEquals($task->user->id, $this->user->id);

    }

    public function test_task_unique_id_is_generated()
    {

        $task = Task::factory()->forUser($this->user)->create();

        $this->assertNotNull($task->task_unique_id);

        $this->assertIsString($task->task_unique_id);

        $this->assertTrue(strlen($task->task_unique_id) > 0);
        
    }

    public function test_tags_is_array_of_strings()
    {
        
        $task = Task::factory()->create([
            
            'tags' => ['frontend', 'backend', 'bug']
            
        ]);

        $this->assertIsArray($task->tags);
        
        foreach ($task->tags as $tag) {
            
            $this->assertIsString($tag);
            
        }
        
    }

    public function test_assigned_to_is_array_of_strings()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create();

        $task->users()->sync([$user->id]);

        $assignedUsers = $task->users()->pluck('unique_id')->toArray();

        $this->assertIsArray($assignedUsers);

        foreach ($assignedUsers as $userId) {

            $this->assertIsString($userId);

        }

    }

    public function test_description_is_not_more_than_100_words()
    {
        $description = str_repeat('word ', 90);

        $task = Task::factory()->create([

            'description' => $description

        ]);
    
        $wordCount = str_word_count($task->description);
    
        $this->assertLessThanOrEqual(100, $wordCount, "The description should not be more than 100 words.");


    }

    public function test_task_is_created_successfully()
    {
        $task = Task::factory()->create();

        $this->assertDatabaseHas('tasks', [

            'id' => $task->id,

            'title' => $task->title

        ]);

    }

    public function test_task_is_updated_successfully()
    {
        $task = Task::factory()->create();

        $updatedTitle = 'Updated Title';

        $task->update(['title' => $updatedTitle]);

        $this->assertDatabaseHas('tasks', [

            'id' => $task->id,

            'title' => $updatedTitle
            
        ]);
    }

    public function test_task_can_be_assigned_to_another_user_successfully()
    {

        $user = User::factory()->create();

        $task = Task::factory()->create();

        $task->users()->sync([$user->id]);

        $this->assertDatabaseHas('task_user_assignments', [

            'task_id' => $task->id,

            'user_id' => $user->id

        ]);

        $newUser = User::factory()->create();

        $task->users()->sync([$newUser->id]);

        $this->assertDatabaseHas('task_user_assignments', [

            'task_id' => $task->id,

            'user_id' => $newUser->id

        ]);
        $this->assertDatabaseMissing('task_user_assignments', [

            'task_id' => $task->id,

            'user_id' => $user->id

        ]);

    }

    public function test_assigned_user_unique_ids_must_exist_in_users_table()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create();

        $task->users()->attach($user->id);

        $assignedUserUniqueIds = User::whereIn('id', $task->users->pluck('id'))->pluck('unique_id')->toArray();

        foreach ($assignedUserUniqueIds as $uniqueId) {

            $this->assertDatabaseHas('users', ['unique_id' => $uniqueId]);

        }

    }
}
