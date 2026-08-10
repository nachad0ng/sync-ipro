<?php

return [

    'wsdl' => env('SOAP_WSDL'),

    'user' => env('SOAP_USERNAME'),

    'pass' => env('SOAP_PASSWORD'),

    'trace' => env('SOAP_TRACE', true),

    'keep_alive' => false, // Disables persistent connections

    'connection_timeout' => 120,

    'dataset' => [
        'schema' => [
            'setting_id'   => 'channel_id',
            'setting_name' => 'TRP',
        ],
        'any' => '',
    ],

];