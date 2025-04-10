<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $connection = 'mysql';
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'tenant_id',
    ];

    // Optional: relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
