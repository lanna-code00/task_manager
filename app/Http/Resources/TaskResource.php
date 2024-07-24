<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $routeName = $request->route()->getName();

        $fetch_all_tasks_route = $routeName === 'fetch-all-tasks';

        return [

            'task_unique_id' => $this->task_unique_id,

            'title' => $this->title,

            'description' => $this->description,

            'status' => $this->status,

            'due_date' => $this->due_date,

            'finished_date' => $this->completion_date,

            'start_date' => $this->start_date,

            'attachments' => $this->attachments,

            'priority' => $this->priority,

            'tags' => $this->tags,

            'created_at' => $this->created_at,

            'meta' => $this->meta,

            'user' => $this->when($fetch_all_tasks_route, $this->user),

            'assigned_users' => $this->users->map(function ($user) {
                return [
                    'name' => $user->name,
                    'unique_id' => $user->unique_id,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ];
            }),

        ];
    }
}
