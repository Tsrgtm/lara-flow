<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingAccount extends Model
{
    protected $fillable = ['disk_limit_mb', 'bandwidth_limit_mb', 'database_limit', 'email_limit', 'domain_limit', 'is_suspended'];

    protected $casts = [
        'disk_limit_mb' => 'integer',
        'bandwidth_limit_mb' => 'integer',
        'database_limit' => 'integer',
        'email_limit' => 'integer',
        'domain_limit' => 'integer',
        'is_suspended' => 'boolean',
    ];

    public function domains(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
