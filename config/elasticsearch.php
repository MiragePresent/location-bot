<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch host urls.
    |--------------------------------------------------------------------------
    |
    | Set the hosts to use for connecting to elasticsearch.
    |
    */
    'hosts' => [ env('ELASTIC_HOST', '127.0.0.1') .':' . env('ELASTIC_PORT', 9200), ],

    /*
     * The prefix to use for index names
     */
    'index_prefix' => env('ELASTICSEARCH_INDEX_PREFIX', ''),
];
