<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;
    protected $connection = 'mysql';

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role_as') // Include the pivot data
            ->withTimestamps(); // Include timestamps if needed
    }
    public function domains()
    {
        return $this->hasMany(Domain::class, 'tenant_id', 'id');
    }
}
