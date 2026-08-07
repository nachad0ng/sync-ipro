<?php

namespace App\Services;

use App\Models\SyncJob;

class SyncJobRuntimeService
{
    public function markQueued(SyncJob $job): void
    {
        $job->update([
            'status'    => 'queued',
            'queued_at' => now(),
        ]);
    }

    public function markRunning(SyncJob $job): void
    {
        $job->update([
            'status'     => 'running',
            'started_at' => now(),
        ]);
    }

    public function markSuccess(
        SyncJob $job,
        int $duration,
        string $message
    ): void {

        $job->update([
            'status'         => 'idle',
            'queued_at'      => null,
            'started_at'     => null,
            'last_execute'   => now(),
            'last_duration'  => $duration,
            'last_status'    => 'success',
            'last_message'   => $message,
        ]);
    }

    public function markFailed(
        SyncJob $job,
        int $duration,
        string $message
    ): void {

        $job->update([
            'status'         => 'idle', // runtime selesai
            'queued_at'      => null,
            'started_at'     => null,
            'last_duration'  => $duration,
            'last_status'    => 'failed',
            'last_message'   => $message,
        ]);
    }
}