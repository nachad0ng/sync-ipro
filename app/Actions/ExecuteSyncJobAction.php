<?php

namespace App\Actions;

use App\Contracts\SoapClientInterface;
use App\Models\SyncJob;
use App\Models\SyncLog;
use Carbon\Carbon;

class ExecuteSyncJobAction
{
    public function __construct(
        protected SoapClientInterface $soap
    ) {}

    public function execute(SyncJob $job): array
    {
        $start = microtime(true);

        try {

            $rows = $this->soap->execute(
                $job->database,
                $job->sql
            );

            $duration = round((microtime(true) - $start) * 1000);

            SyncLog::create([
                'sync_job_id' => $job->id,
                'status' => 'success',
                'duration' => $duration,
                'message' => count($rows).' rows'
            ]);

            $job->update([
                'last_execute' => now()
            ]);

            return $rows;

        } catch (\Throwable $e) {

            $duration = round((microtime(true) - $start) * 1000);

            SyncLog::create([
                'sync_job_id' => $job->id,
                'status' => 'failed',
                'duration' => $duration,
                'message' => $e->getMessage()
            ]);

            throw $e;
        }

    }
}