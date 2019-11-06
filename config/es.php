<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Elasticsearch Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the Elasticsearch connections below you wish
    | to use as your default connection for all work. Of course.
    |
    */

    'default' => env('ELASTIC_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the Elasticsearch connections setup for your application.
    | Of course, examples of configuring each Elasticsearch platform.
    |
    */

    'connections' => [

        'default' => [

            'servers' => [

                [
                    "host" => env("ELASTIC_HOST", "127.0.0.1"),
                    "port" => env("ELASTIC_PORT", 9200),
                    'user' => env('ELASTIC_USER', ''),
                    'pass' => env('ELASTIC_PASS', ''),
                    'scheme' => env('ELASTIC_SCHEME', 'http'),
                ]

            ],

            'index' => env('ELASTIC_INDEX', 'locations'),

//            'client' => [
//                'custom' => [
//                    'include_type_name' => true
//                ]
//            ],
            // Elasticsearch handlers
            // 'handler' => new MyCustomHandler(),
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Indices
    |--------------------------------------------------------------------------
    |
    | Here you can define your indices, with separate settings and mappings.
    | Edit settings and mappings and run 'php artisan es:index:update' to update
    | indices on elasticsearch server.
    |
    | 'my_index' is just for test. Replace it with a real index name.
    |
    */

    'indices' => [

        'locations' => [

            'settings' => [
                "number_of_shards" => 1,
                "number_of_replicas" => 0,
            ],

            'mappings' => [
                'churches' => [
                    'include_type_name' => false,
                    "properties" => [
                        'name' => ['type' => 'string', 'analyzer' => 'ukrainian' ],
                        'city' => ['type' => 'string', 'analyzer' => 'ukrainian']
                    ]
                ],
                "cities" => [
                    'include_type_name' => false,
                    "properties" => [
                        "name" => ["type" => "string", 'analyzer' => 'ukrainian']
                    ]
                ]
            ]
        ]
    ]
];
