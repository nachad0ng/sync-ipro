<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    //
    protected $fillable=[
        'sync_job_id',
        'sql',
        'status',
        'duration',
        'message',
        'step_no',
        'step_name'
    ];


    public function job()
    {
        return $this->belongsTo(SyncJob::class,'sync_job_id');
    }
}
