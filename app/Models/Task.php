<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            $task->task_unique_id = self::generateUniqueId();
        });
    }

    public static function generateUniqueId()
    {
        do {
            $uniqueId = Str::random(8) . '_' . Str::random(16);
        } while (self::where('task_unique_id', $uniqueId)->exists());

        return $uniqueId;
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
