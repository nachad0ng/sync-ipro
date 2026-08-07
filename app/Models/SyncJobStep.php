<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncJobStep extends Model
{
    //
    protected $fillable = [
        'sync_job_id',
        'step_no',
        'name',
        'sql',
        'active',
    ];

    public function job()
    {
        return $this->belongsTo(
            SyncJob::class,
            'sync_job_id'
        );
    }
}
