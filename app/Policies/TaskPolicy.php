<?php

namespace App\Policies;

use App\Exceptions\UnauthorizedException;
use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\Response;
class TaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    
    }
     /**
     * Determine if the user can view the task.
     */
    public function view(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new UnauthorizedException();
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new UnauthorizedException();
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(User $user, Task $task): Response
    {
        return $user->id === $task->user_id
            ? Response::allow()
            : throw new UnauthorizedException();
    }
}
