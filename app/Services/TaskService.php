<?php

namespace App\Services;
use App\Models\Task;
use App\Utils\HtmlSanitize;

class TaskService {

    function __construct(Task $task)
    {
       $this->task = $task;
    }


    private function authUser()
    {
      return auth()->user();
    }

    function fetchAllTasks()
    {
        try {

            return $this->task->with('user')->latest()->get();

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([

                'status' => 'error', 'message' => 'An unexpected error occurred.'

            ], 500);
        }
    }

    function createTask($data)
    {
        try {

            $data['title'] = HtmlSanitize::sanitizeHtml($data['title']);
            
            $data['description'] = HtmlSanitize::sanitizeHtml($data['description']);

            $_task = $this->authUser()->tasks()->create($data);

            return response()->json([

                'status' => 'success',

                'message' => 'Task created successfully',

                'data' => $_task

            ], 201);

        } catch (\Throwable $th) {
            
             return response()->json([

                 'status' => 'error', 'message' => 'An unexpected error occurred.'

             ], 500);
        }

    }

    function updateTask($data, $task)
    {
        try {

            $data['title'] = HtmlSanitize::sanitizeHtml($data['title']);
            
            $data['description'] = HtmlSanitize::sanitizeHtml($data['description']);

            return response()->json([

                'status' => 'success',

                'message' => 'Task updated successfully',

                'data' => tap($task)->update($data)

            ], 200);


        } catch (\Throwable $th) {
  
            return response()->json([

                'status' => 'error', 'message' => 'An unexpected error occurred.'

            ], 500);
        }

    }

    // function show($task)
    // {
  
    //     return $task;
    // }


    public function delete(Task $task)
    {

        $task->delete();

        return response()->json([

            'status' => 'success',

            'message' => 'Task deleted successfully'

        ], 200);
    }
    
}