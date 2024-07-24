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
        $_tasks = auth()->user()->tasks()->latest()->paginate(12);

        return response()->json([
            
            'status' => 'success',

            'data' => TaskResource::collection($_tasks)

        ], 200);
    }


    public function fetchAllTasks()
    {
        $_tasks = $this->taskService->fetchAllTasks();

        return response()->json([
            
            'status' => 'success',

            'data' => TaskResource::collection($_tasks)

        ], 200);
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

        // $_task = $this->taskService->show($task);

        return response()->json([
            
            'status' => 'success',

            'data' => TaskResource::make($task)

        ], 200);

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
