<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Username
    |--------------------------------------------------------------------------
    |
    | Username or number.
    | This field can be dynamically set through service class.
    |
    */

    'username' => '',

    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    |
    | This field can be dynamically set through service class.
    |
    */

    'password' => '',

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Keep logging every response.
    |
    */

    'log' => true,

    'send_sms' => [

        /*
        |--------------------------------------------------------------------------
        | Service Type
        |--------------------------------------------------------------------------
        |
        | Netgsm lets developers to choose between 3 methods to consume API.
        | These are XML POST and HTTP GET.
        | Options are xml, http.
        | Default or empty option is xml.
        |
        */

        'service' => 'xml',

        'params' => [

            /*
            |--------------------------------------------------------------------------
            | Encoding
            |--------------------------------------------------------------------------
            |
            | Toggle if Turkish character support is required.
            | Calculation SMS length and pricing will be affected.
            |
            */

            'encoding' => true,

            /*
            |--------------------------------------------------------------------------
            | Header
            |--------------------------------------------------------------------------
            |
            | Default is username field.
            | This field can be dynamically set through \TCGunel\Netgsm\Sms\Params class.
            |
            */

            'header' => '',

            /*
            |--------------------------------------------------------------------------
            | Filter
            |--------------------------------------------------------------------------
            |
            | This field can be dynamically set through \TCGunel\Netgsm\Sms\Params class.
            |
            */

            'filter' => '',

            /*
            |--------------------------------------------------------------------------
            | Branch code
            |--------------------------------------------------------------------------
            |
            | This field can be dynamically set through \TCGunel\Netgsm\Sms\Params class.
            |
            */

            'bayikodu' => '',
        ],
    ],

    'credit_query' => [

        'service' => 'xml',

    ],

    'package_campaign_query' => [

        'service' => 'xml',

    ],

];
