<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SyncJob;
use App\Actions\ExecuteSyncJobAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;

#[Signature('sync:run {id}')]
#[Description('Execute one Sync Job')]
class SyncRunCommand extends Command
{
    public function __construct(
        protected ExecuteSyncJobAction $action
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $job = SyncJob::find($this->argument('id'));

        if (!$job) {
            $this->error("Job tidak ditemukan.");
            return self::FAILURE;
        }

        $this->line(str_repeat('-', 50));
        $this->info("SOAP ENGINE");
        $this->line(str_repeat('-', 50));

        $this->table(
            ['Property', 'Value'],
            [
                ['Job', $job->name],
                ['Database', $job->database],
                ['Started', now()],
            ]
        );

        $this->newLine();

        $this->info("Executing SQL ...");

        try {

            $rows = $this->action->execute($job);

            $count = is_array($rows)
                ? count($rows)
                : 0;

            $this->newLine();

            $this->info("SUCCESS");

            $this->table(
                ['Result', 'Value'],
                [
                    ['Rows', $count],
                    ['Finished', now()],
                ]
            );

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->newLine();

            $this->error("FAILED");

            $this->error($e->getMessage());

            return self::FAILURE;
        }

    }
}