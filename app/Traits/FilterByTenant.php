<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait FilterByTenant
{
    protected static function bootFilterByTenant()
    {
        // Add a global scope to filter queries by the current tenant
        static::addGlobalScope('tenant_filter', function (Builder $builder) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $builder->where('tenant_id', Auth::user()->tenant_id);
            }
        });

        // Automatically set tenant_id on model creation
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });
    }
}
