<?php

namespace App\Contracts;

interface SoapClientInterface
{
    public function execute(string $database, string $sql): mixed;
}