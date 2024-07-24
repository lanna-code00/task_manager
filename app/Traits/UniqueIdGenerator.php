<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UniqueIdGenerator
{
    /**
     * Boot the trait to add event listeners.
     *
     * @return void
     */
    protected static function bootUniqueIdGenerator()
    {
        static::creating(function ($model) {
            if (method_exists($model, 'generateUniqueId')) {
                $model->{$model->uniqueIdColumn} = $model->generateUniqueId();
            }
        });
    }

    /**
     * Generate a unique ID.
     *
     * @param  int  $segment1Length
     * @param  int  $segment2Length
     * @param  string  $delimiter
     * @return string
     */
    public function generateUniqueId($segment1Length = 12, $segment2Length = 24, $delimiter = '-')
    {
        do {
            $uniqueId = Str::random($segment1Length) . $delimiter . Str::random($segment2Length);
        } while (self::where($this->uniqueIdColumn, $uniqueId)->exists());
    
        return $uniqueId;
    }
    
}
