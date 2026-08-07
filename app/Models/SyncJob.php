<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncJob extends Model
{
    //
    protected $fillable = [
        'name',
        'database',
        'sql',
        'interval',
        'timeout',
        'retry',
        'active',
        'last_execute'
    ];

    protected $casts = [
        'last_execute' => 'datetime',
    ];
    

    public function logs()
    {
        return $this->hasMany(SyncLog::class);
    }
}
