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
        return [
            'task_unique_id' => $this->task_unique_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'meta' => $this->meta
        ];
    }
}
