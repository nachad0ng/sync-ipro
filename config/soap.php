<?php

return [

    'wsdl' => env('SOAP_WSDL'),

    'user' => env('SOAP_USERNAME'),

    'pass' => env('SOAP_PASSWORD'),

    'trace' => env('SOAP_TRACE', true),

    'dataset' => [
        'schema' => [
            'setting_id'   => 'channel_id',
            'setting_name' => 'TRP',
        ],
        'any' => '',
    ],

];