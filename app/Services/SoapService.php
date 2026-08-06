<?php

namespace App\Services;

use App\Support\SoapResult;
use App\Contracts\SoapClientInterface;
use SoapClient;
use Exception;

class SoapService implements SoapClientInterface
{
    protected SoapClient $client;

    public function __construct()
    {
        $config = config('soap');

        $this->client = new SoapClient(
            $config['wsdl'],
            [
                'trace' => $config['trace'],
            ]
        );

        $this->client->__setLocation($config['wsdl']);
    }

    public function execute(string $database, string $sql): mixed
    {
        $query = "USE [$database];\n\n$sql";

        $params = [

            'dataset' => config('soap.dataset'),

            'pUserID' => config('soap.user'),

            'pPass' => config('soap.pass'),

            'SQLString' => $query,

            'message' => ''

        ];

        $result = $this->client
            ->uiQuery_RetrieveByte($params);

        return $this->parseResult($result);
    }

    protected function parseResult($result): array
    {
        if (!isset($result->dataset->any)) {
            return [];
        }

        $xml = simplexml_load_string(
            $result->dataset->any
        );

        if (!$xml) {
            return [];
        }

        $json = json_decode(
            json_encode($xml),
            true
        );

        if (empty($json)) {
            return [];
        }
        
        return SoapResult::rows($json);
    }
}