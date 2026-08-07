<?php

namespace App\Actions;

use App\Services\SyncJobRuntimeService;
use App\Contracts\SoapClientInterface;
use App\Models\SyncJob;
use App\Models\SyncLog;

class ExecuteSyncJobAction
{
    public function __construct(
        protected SoapClientInterface $soap,
        protected SyncJobRuntimeService $runtime,
    ) {}

    public function execute(SyncJob $job): array
    {
        
        $this->runtime->markRunning($job);
        
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

            $this->runtime->markSuccess(
                $job,
                $duration,
                $message
            );

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

            $this->runtime->markFailed(
                $job,
                $duration,
                $e->getMessage()
            );

            throw $e;
        }

    }
}