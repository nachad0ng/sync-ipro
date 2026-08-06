<?php

namespace App\Support;

class SoapResult
{
    public static function rows(array $json): array
    {
        if (isset($json['NewDataSet']['dt'])) {
            return $json['NewDataSet']['dt'];
        }

        if (isset($json['NewDataSet']['Table'])) {
            return $json['NewDataSet']['Table'];
        }

        return $json;
    }
}