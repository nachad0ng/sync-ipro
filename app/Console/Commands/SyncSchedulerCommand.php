<?php

namespace App\Console\Commands;

use App\Services\SyncJobRuntimeService;
use App\Jobs\ExecuteSyncJob;
use App\Models\SyncJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Actions\ExecuteSyncJobAction;

#[Signature('sync:scheduler')]
#[Description('Run all Sync Jobs')]
class SyncSchedulerCommand extends Command
{
    public function __construct(
        protected ExecuteSyncJobAction $action,
        protected SyncJobRuntimeService $runtime
    )
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line("");
        $this->line(now());
        $this->line("-------------------------");

        $this->recoverJobs();

        $jobs = SyncJob::where('active', true)
            ->get();

        foreach ($jobs as $job) {

            if ($this->shouldRun($job)) {

                $this->info("Running : {$job->name}");

                try {

                    ExecuteSyncJob::dispatch($job->id);

                    $this->runtime->markQueued($job);

                    $this->info("Queued");

                } catch (\Throwable $e) {

                    $this->error($e->getMessage());

                }

            } else {

                $this->line("Skip : {$job->name}");

            }

        }

        return self::SUCCESS;
    }

    protected function recoverJobs(): void
    {
        SyncJob::query()
            ->whereIn('status', ['queued', 'running'])
            ->where('updated_at', '<', now()->subMinutes(10))
            ->update([
                'status' => 'idle'
            ]);
    }

    protected function shouldRun(SyncJob $job): bool
    {
        if (!$job->active) {
            return false;
        }

        if (in_array($job->status, ['queued', 'running'])) {
            return false;
        }

        if ($job->last_execute === null) {
            return true;
        }

        return $job->last_execute
            ->copy()
            ->addMinutes($job->interval)
            ->lte(now());
    }
}
