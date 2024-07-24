<?php

namespace App\Services;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Utils\HtmlSanitize;
use Illuminate\Support\Facades\Storage;

class TaskService {

    function __construct(Task $task)
    {
       $this->task = $task;
    }


    private function authUser()
    {

      return auth()->user();

    }

    public function index()
    {
        $_tasks = auth()->user()->tasks()->latest()->paginate(12);

        return response()->json([
            
            'status' => 'success',

            'data' => TaskResource::collection($_tasks)

        ], 200);
    }


    public function fetchMyAssignedTasks()
    {

        $_tasks = auth()->user()->assignedTasks;

        return response()->json([

            'status' => 'success',

            'data' => TaskResource::collection($_tasks)
        ]);

    }


    private function deleteOldFiles ($task, $newAttachments = [])
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

            $_tasks = $this->task->with('user:unique_id,name,email')->latest()->paginate(12);

            return response()->json([
            
                'status' => 'success',
    
                'data' => TaskResource::collection($_tasks)
    
            ], 200);

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
                   
                   $user = User::where('unique_id', $data['assigned_to'])->first();
                   
                   $data['assigned_to'] = $user?->id;
   
               }
   
               $_task = $this->authUser()->tasks()->create($data);
   
               // Assign the task if there's an assigned user
               if (isset($data['assigned_to']) && is_array($data['assigned_to'])) {
                   
                   $userIds = User::whereIn('unique_id', $data['assigned_to'])->pluck('id');
                   
                   $_task->users()->attach($userIds);
               
               }
    
                return response()->json([
   
                    'status' => 'success',
   
                    'message' => 'Task created successfully',
   
                    'data' => $_task
   
                ], 201);
            });

            return $_create_task;

        } catch (\Throwable $th) {

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