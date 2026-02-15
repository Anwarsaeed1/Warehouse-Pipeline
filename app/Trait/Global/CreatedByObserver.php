<?php

namespace App\Trait\Global;

use Illuminate\Database\Eloquent\Model;

trait CreatedByObserver
{
    /**
     * Boot the created_by observer.
     *
     * When creating a model, this sets the created_by field to the current user id.
     */
    public static function bootCreatedByObserver(): void
    {
        static::creating(static fn(Model $model) => $model->created_by = auth()->id());
    }
}
