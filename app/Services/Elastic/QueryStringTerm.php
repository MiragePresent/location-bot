<?php

namespace App\Services\Elastic;

use Nord\Lumen\Elasticsearch\Search\Query\QueryDSL;

/**
 * Class QueryStringTerm
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.05.2020
 */
class QueryStringTerm extends QueryDSL
{
    /**
     * @var string
     */
    private $query;

    /**
     * QueryStringTerm constructor.
     *
     * @param string $query Search query value
     */
    public function __construct(string $query = '')
    {
        $this->query = $query;
    }

    /**
     * Set search query value
     *
     * @param string $query
     *
     * @return $this
     */
    public function setQuery(string $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return ['query_string' => ['query' => $this->query]];
    }
}
