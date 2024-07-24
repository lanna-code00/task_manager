<?php

namespace App\Models;

use App\Traits\UniqueIdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory, UniqueIdGenerator;

    protected $uniqueIdColumn = 'task_unique_id';
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'due_date',
        'priority',
        'tags',
        'attachments',
        'user_id',
        'completion_date',
        'status',
        'meta'
    ];

    protected function casts(): array
    {
        return [

            'meta' => 'array',

            'tags' => 'array',

            'due_date' => 'datetime',

            'completed_date' => 'datetime',

            'start_date' => 'datetime',

            'attachments' => 'array',

            'created_at' => 'datetime'

        ];
    }

    protected $hidden = [
        'user_id',
        'id',
    ];
    
    public function getRouteKeyName()
    {

        return 'task_unique_id';

    }

    public function users(): BelongsToMany
    {

        return $this->belongsToMany(User::class, 'task_user_assignments', 'task_id', 'user_id')
                   
                    ->withTimestamps()
                    
                    ->withPivot('user_id');

    }
    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class);

    }


}
