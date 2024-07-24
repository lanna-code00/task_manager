<?php

namespace App\Http\Requests;

use App\Enums\PriorityStatus;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['bail', 'required', 'min:5', 'max:60'],
            'description' => ['bail', 'required', 'min:10', 'max:800'],
            'status' => ['bail', 'required', Rule::in([TaskStatus::COMPLETED->value, TaskStatus::IN_PROGRESS->value, TaskStatus::PENDING->value])],
            'start_date' => ['bail', 'nullable', 'date'],
            'due_date' => ['bail', 'nullable', 'date'],
            'priority' => ['bail', 'nullable', Rule::in([PriorityStatus::LOW->value, PriorityStatus::MEDIUM->value, PriorityStatus::URGENT->value, PriorityStatus::HIGH->value])],
            'assigned_to' => ['bail', 'nullable', 'exists:users,unique_id', 'array'],
            'tags' => ['bail', 'nullable', 'array'],
            'tags.*' => ['bail', 'string', 'max:40'],
            'attachments' => ['bail', 'nullable', 'array'],
            'attachments.*' => ['bail','file', 'mimes:jpg,png,pdf,doc,docx', 'max:2048'],
            'completion_date' => ['bail', 'nullable', 'after_or_equal:start_date', 'date'],
            'meta' => ['bail','nullable', 'json']
        ];
    }


    public function attributes()
    {
        return [
            'assigned_to' => 'assignee',
        ];
    }
}
