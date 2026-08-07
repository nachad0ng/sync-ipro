<?php

namespace App\Actions;

use App\Contracts\SoapClientInterface;
use App\Models\SyncJob;
use App\Models\SyncLog;
use App\Services\SyncJobRuntimeService;

class ExecuteSyncJobAction
{
    public function __construct(
        protected SoapClientInterface $soap,
        protected SyncJobRuntimeService $runtime,
    ) {}

    public function execute(SyncJob $job): void
    {
        $this->runtime->markRunning($job);

        $steps = $job->steps()
            ->where('active', true)
            ->orderBy('step_no')
            ->get();

        $startJob = microtime(true);

        foreach ($steps as $step) {

            $startStep = microtime(true);

            try {

                $rows = $this->soap->execute(
                    $job->database,
                    $step->sql
                );

                $duration = round(
                    (microtime(true) - $startStep) * 1000
                );

                $message = 'Rows : ' . (
                    is_countable($rows)
                        ? count($rows)
                        : 0
                );

                SyncLog::create([

                    'sync_job_id' => $job->id,

                    'step_no' => $step->step_no,

                    'step_name' => $step->name,

                    'sql' => $step->sql,

                    'status' => 'success',

                    'duration' => $duration,

                    'message' => $message,

                ]);

            } catch (\Throwable $e) {

                $duration = round(
                    (microtime(true) - $startStep) * 1000
                );

                SyncLog::create([

                    'sync_job_id' => $job->id,

                    'step_no' => $step->step_no,

                    'step_name' => $step->name,

                    'sql' => $step->sql,

                    'status' => 'failed',

                    'duration' => $duration,

                    'message' => $e->getMessage(),

                ]);

                $this->runtime->markFailed(
                    $job,
                    round((microtime(true) - $startJob) * 1000),
                    $e->getMessage()
                );

                throw $e;
            }

        }

        $this->runtime->markSuccess(

            $job,

            round((microtime(true) - $startJob) * 1000),

            'Completed'

        );
    }
}