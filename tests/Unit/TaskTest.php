<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
