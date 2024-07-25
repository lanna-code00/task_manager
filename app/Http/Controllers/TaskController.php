<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskFormRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->taskService->index();
    }
    
    public function fetchAllTasks()
    {

        return $this->taskService->fetchAllTasks();

    }

    public function fetchMyAssignedTasks()
    {
        return $this->taskService->fetchMyAssignedTasks();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskFormRequest $request)
    {
        $_task = $this->taskService->createTask($request->validated());

        return $_task;
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        \Gate::authorize('view', $task);

        return $this->taskService->show($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskFormRequest $request, Task $task)
    {
        \Gate::authorize('update', $task);

        return $this->taskService->updateTask($request->validated(), $task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        \Gate::authorize('delete', $task);

        return $this->taskService->delete($task);
    }
}
