<?php

namespace App\Services;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Utils\HtmlSanitize;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TaskService {

    function __construct(Task $task)
    {
       $this->task = $task;
    }


    protected function authUser()
    {

      return auth()->user();

    }

    public function index()
    {
        $_tasks = auth()->user()->tasks()->latest();

        return response()->json([
            
            'status' => 'success',

            'data' => TaskResource::collection($_tasks->paginate(12))->response()->getData(true)

        ], 200);
    }


    public function fetchMyAssignedTasks()
    {
        try {
            $_tasks = $this->authUser()->assignedTasks()->latest()->paginate(12);
    
            return response()->json([
    
                'status' => 'success',
    
                'data' => TaskResource::collection($_tasks)->response()->getData(true)
            ]);

        } catch (\Throwable $th) {
            throw $th;
            // Log::error('Fetching assigned tasks failed', ['error' => $th->getMessage()]);

        }

    }
    protected function deleteOldFiles ($task, $newAttachments = [])
    {
        if ($task->attachments) {

            $oldAttachments = array_diff($task->attachments, $newAttachments);
           
            foreach ($oldAttachments as $oldAttachment) {
                
                Storage::disk('public')->delete($oldAttachment);
            
            }
        }
    }

    function fetchAllTasks()
    {
        try {

            $_tasks = $this->task->with('user')->latest()->paginate(12);

            return response()->json([
            
                'status' => 'success',
    
                'data' => TaskResource::collection($_tasks)->response()->getData(true)
    
            ], 200);

        } catch (\Throwable $th) {
            // throw $th;
            return response()->json([

                'status' => 'error', 'message' => 'An unexpected error occurred.'

            ], 500);
        }
    }

    function createTask($data)
    {
        try {

            $_create_task = \DB::transaction(function() use($data) {

                $data['title'] = HtmlSanitize::sanitizeHtml($data['title']);
                
                $data['description'] = HtmlSanitize::sanitizeHtml($data['description']);
    
                // Handling file attachments using array_map
                $data['attachments'] = array_map(
                  
                   fn($attachment) => $attachment->store('attachments', 'public'),
                  
                   $data['attachments'] ?? []
               );
    
                   // Check if there's an assigned user
                   if (isset($data['assigned_to']) && is_array($data['assigned_to'])) {
                    
                    $userIds = User::whereIn('unique_id', $data['assigned_to'])->pluck('id')->unique()->toArray();
               
                }

                Log::info("user", ["message" => auth()->user()]);
    
                $_task = auth()->user()->tasks()->create($data);
    
                if (isset($data['assigned_to']) && is_array($data['assigned_to'])) {
                   
                    $_task->users()->sync($userIds);

                }
    
                return response()->json([
   
                    'status' => 'success',
   
                    'message' => 'Task created successfully',
   
                    'data' => $_task
   
                ], 201);
            });

            return $_create_task;

        } catch (\Throwable $th) {

            Log::error('Task creation failed', ['error' => $th->getMessage()]);

             return response()->json([

                 'status' => 'error', 'message' => 'An unexpected error occurred.'

             ], 500);
        }

    }

    function updateTask($data, $task)
    {
        try {

            $_update_task = \DB::transaction(function() use($data, $task) {

                $data['title'] = isset($data['title']) ? HtmlSanitize::sanitizeHtml($data['title']) : $task->title;
           
                $data['description'] = isset($data['description']) ? HtmlSanitize::sanitizeHtml($data['description']) : $task->description;
        
                //for tasks that have files
                $newAttachments = isset($data['attachments'])
    
                ? array_map(fn($attachment) => $attachment->store('attachments', 'public'), $data['attachments'])
                
                : [];
    
                $this->deleteOldFiles($task, $newAttachments);
        
                $data['attachments'] = $newAttachments;
        
                $task->update($data);
        
                if (isset($data['assigned_to'])) {
                   
                    $userIds = User::whereIn('unique_id', $data['assigned_to'])->pluck('id')->toArray();
                //    return $userIds;
                    $task->users()->sync($userIds);
    
                }
            
                return response()->json([
    
                    'status' => 'success',
    
                    'message' => 'Task updated successfully',
                    
                    'data' => TaskResource::make($task)
    
                ], 200);

            });

         return $_update_task;

        } catch (\Throwable $th) {
            // throw $th;
  
            return response()->json([

                'status' => 'error', 'message' => 'An unexpected error occurred.'

            ], 500);
        }

    }

    function show($task)
    {

      try {

        return response()->json([
            
            'status' => 'success',

            'data' => TaskResource::make($task)

        ], 200);

      } catch (\Throwable $th) {

         return response()->json([

                'status' => 'error', 'message' => 'An unexpected error occurred.'

            ], 500);

      }

    }


    public function delete(Task $task)
    {

        $this->deleteOldFiles($task);

        $task->delete();

        return response()->json([

            'status' => 'success',

            'message' => 'Task deleted successfully'

        ], 200);
    }

   
}