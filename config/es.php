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
                    'host' => env('ELASTIC_HOST', '127.0.0.1'),
                    'port' => env('ELASTIC_PORT', 9200),
                    'user' => env('ELASTIC_USER', ''),
                    'pass' => env('ELASTIC_PASS', ''),
                    'scheme' => env('ELASTIC_SCHEME', 'http'),
                ]

            ],

            'index' => env('ELASTIC_INDEX', 'my_index'),

            'custom' => [
                'include_type_name' => true,
            ],

            // Elasticsearch handlers
//             'handler' => \Elasticsearch\ClientBuilder::singleHandler(),

            'logging' => [
                'enabled'   => env('ELASTIC_LOGGING_ENABLED',false),
                'level'     => env('ELASTIC_LOGGING_LEVEL','all'),
                'location'  => env('ELASTIC_LOGGING_LOCATION',base_path('storage/logs/elasticsearch.log'))
            ],
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
            'aliases' => [
                \App\Models\Church::ELASTIC_INDEX,
            ],
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,

                'index' => [
                    'analysis' => [
                        "analyzer" => [
                            "latin" => [
                                "tokenizer" => "standard",
                                "filter" => ["lowercase", "translt"],
                            ],
                            'keyboard_layout' => [
                                'tokenizer' => 'standard',
                                'char_filter' => [
                                    'ukr_to_eng'
                                ],
                            ],
                            'cities' => [
                                'tokenizer' => 'standard',
                                'type' => 'custom',
                                'filters' => [
                                    "standard",
                                    "lowercase",
                                    'cities',
                                    "stop"
                                ],
                            ],
                        ],
                        'char_filter' => [
                            'ukr_to_eng' => [
                                'type' => 'mapping',
                                'mappings' => [
                                    "й => q","ц => w","у => e","к => r","е => t","н => y","г => u",
                                    "ш => i","щ => o","з => p","х => [","Х => {","ї => ]","Ї => }",
                                    "/ => |","ё => `","Ё => ~","ф => a","і => s","в => d","а => f",
                                    "п => g","р => h","о => j","л => k","д => l","ж => ;","Ж => :",
                                    "є => '","Є => \"","я => z","ч => x","с => c","м => v","и => b",
                                    "т => n","ь => m","б => ,","Б => <","ю => .","Ю => >",". => /",
                                    ", => ?","\" => @","№ => #","; => $",": => ^","? => &",
                                ],
                            ],
                        ],
                        "filter" => [
                            "translt" => [
                                "type" => "icu_transform",
                                "id" => "Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC"
                            ],
                            'cities' => [
                                'type'  => 'synonym',
                                'synonym_path' => 'synonyms/cities.txt'
                            ],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                \App\Models\Church::ELASTIC_INDEX => [
                    'properties' => [
                        'city' => [
                            'type' => 'text',
                            'analyzer' => 'ukrainian',
                            'fields' => [
                                'ukrainian' => [
                                    'type' => 'text',
                                    'analyzer' => 'ukrainian',
                                ],
                                'latin' => [
                                    'type' => 'text',
                                    'analyzer' => 'latin',
                                ],
                                'eng' => [
                                    'type' => 'text',
                                    'analyzer' => 'cities',
                                ],
                                'keyboard' => [
                                    'type' => 'text',
                                    'analyzer' => 'keyboard_layout',
                                ],
                            ]
                        ],
//                        'city_latin' => [
//                            'analyzer' => 'latin',
//                            'type' => 'text',
//                        ],
                        'name' => [
                            'type' => 'text',
                            'fields' => [
                                'latin' => [
                                    'type' => 'text',
                                    'analyzer' => 'latin',
                                ]
                            ]
                        ],
                        'address' => [
                            'type' => 'text',
                            'fields' => [
                                'latin' => [
                                    'type' => 'text',
                                    'analyzer' => 'latin',
                                ]
                            ]

                        ],
                        'location' => [
                            'type' => 'geo_point',
                        ],
                    ]
                ]
            ]
        ]
    ]
];
