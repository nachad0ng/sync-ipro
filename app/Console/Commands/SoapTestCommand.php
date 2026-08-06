<?php

namespace App\Console\Commands;

use App\Contracts\SoapClientInterface;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:soap-test-command')]
#[Description('Command description')]
class SoapTestCommand extends Command
{
    public function __construct(private SoapClientInterface $soap){
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $result = $this->soap->execute(
            'e-frm3-transmart',
            'SELECT top 10 * FROM master_acc'
        );

        dd($result);
    }
}
