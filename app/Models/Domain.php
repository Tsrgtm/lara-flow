<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $fillable = ['domain_name', 'root_path','ssl_enabled', 'php_version', 'enable_queue', 'enable_reverb'];

    public function hostingAccount(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HostingAccount::class);
    }

}
