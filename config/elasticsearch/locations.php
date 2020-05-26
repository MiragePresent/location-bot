<?php

return [
    'index' => 'locations',
    'body' => [
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
//            \App\Models\Church::ELASTIC_INDEX => [
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
        ],
//    ],
];
