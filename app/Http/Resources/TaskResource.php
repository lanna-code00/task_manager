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
            'meta' => $this->meta,
            'user' => $this->when($fetch_all_tasks_route, $this->user)
        ];
    }
}
