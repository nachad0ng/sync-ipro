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
        'last_execute' => 'datetime',
    ];
    

    public function logs()
    {
        return $this->hasMany(SyncLog::class);
    }

    public function steps()
    {
        return $this->hasMany(SyncJobStep::class)->orderBy('step_no');
    }
}
