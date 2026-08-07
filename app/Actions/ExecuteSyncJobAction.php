<?php

namespace App\Actions;

use App\Contracts\SoapClientInterface;
use App\Models\SyncJob;
use App\Models\SyncLog;

class ExecuteSyncJobAction
{
    public function __construct(protected SoapClientInterface $soap) {}

    public function execute(SyncJob $job): array
    {
        
        $job->update([
            'status' => 'running',
        ]);
        
        $start = microtime(true);

        try {

            $rows = $this->soap->execute(
                $job->database,
                $job->sql
            );

            $duration = round(
                (microtime(true) - $start) * 1000
            );

            $message = 'Rows : ' . (
                is_countable($rows)
                    ? count($rows)
                    : 0
            );

            SyncLog::create([
                'sync_job_id' => $job->id,
                'sql' => $job->sql,
                'status' => 'success',
                'duration' => $duration,
                'message' => $message
            ]);

            $job->update([
                'status' => 'idle',
                'last_execute'   => now(),
                'last_duration'  => $duration,
                'last_status'    => 'success',
                'last_message'   => $message,
            ]);

            return $rows;

        } catch (\Throwable $e) {

            $duration = round(
                (microtime(true) - $start) * 1000
            );

            SyncLog::create([
                'sync_job_id' => $job->id,
                'sql' => $job->sql,
                'status' => 'failed',
                'duration' => $duration,
                'message' => $e->getMessage(),
            ]);

            $job->update([
                'status' => 'failed',
                'last_duration'  => $duration,
                'last_status'    => 'failed',
                'last_message'   => $e->getMessage(),
            ]);

            throw $e;
        }

    }
}