<?php

namespace App\Services;

class SoapService
{
    protected SoapClient $client;

    public function __construct()
    {
        $this->client = new SoapClient(
            config('services.soap.wsdl')
        );
    }

    public function execute($database,$sql)
    {
        $command = "

            use [$database];

            $sql

        ";

        return $this->client->ExecuteSQL([
            'Command'=>$command
        ]);
    }
}