<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUlidSync
{
    /**
     * Boot the trait to auto-generate ULID on creation.
     */
    protected static function bootHasUlidSync()
    {
        static::creating(function ($model) {
            if (empty($model->ulid)) {
                $model->ulid = (string) Str::ulid();
            }
        });
    }

    /**
     * Scope a query to find a model by its ULID.
     */
    public function scopeByUlid($query, string $ulid)
    {
        return $query->where('ulid', $ulid);
    }
}
