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
        'status',
        'retry',
        'active',
        'last_execute',
        'last_status',
        'last_message',
        'last_duration',
    ];

    protected $casts = [
        'last_status' => 'datetime',
        'last_execute' => 'datetime',
        'last_message' => 'datetime',
        'last_duration' => 'datetime',
    ];
    

    public function logs()
    {
        return $this->hasMany(SyncLog::class);
    }
}
