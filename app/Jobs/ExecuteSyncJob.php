<?php

namespace App\Jobs;

use App\Actions\ExecuteSyncJobAction;
use App\Models\SyncJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Throwable;

class ExecuteSyncJob implements ShouldQueue
{
    use Queueable;

    /**
     * Maksimal retry.
     */
    public int $tries = 3;

    /**
     * Timeout per job.
     */
    public int $timeout = 300;

    /**
     * Jika timeout maka langsung gagal.
     */
    public bool $failOnTimeout = true;

    public function __construct(
        public int $jobId
    ) {
        $this->onQueue('soap');
    }

    public function handle(
        ExecuteSyncJobAction $action
    ): void {

        $job = SyncJob::find($this->jobId);

        if (!$job) {
            return;
        }

        $lock = Cache::lock(
            'sync-job-'.$job->id,
            $this->timeout
        );

        if (!$lock->get()) {

            logger()->warning(
                "Job {$job->id} masih diproses worker lain."
            );

            return;
        }

        try {

            $action->execute($job);

        } finally {

            optional($lock)->release();

        }

    }

    public function failed(Throwable $exception): void
    {
        $job = SyncJob::find($this->jobId);

        if (!$job) {
            return;
        }

        app(\App\Services\SyncJobRuntimeService::class)
            ->markFailed(
                $job,
                0,
                $exception->getMessage()
            );
    }
}