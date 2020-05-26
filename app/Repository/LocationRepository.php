<?php

namespace App\Repository;

use App\Models\Church;
use App\Services\Elastic\GeoDistanceSort;
use App\Services\Elastic\QueryStringTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Nord\Lumen\Elasticsearch\Contracts\ElasticsearchServiceContract;
use Nord\Lumen\Elasticsearch\Search\Query\Compound\BoolQuery;
use Nord\Lumen\Elasticsearch\Search\Query\TermLevel\TermQuery;
use Nord\Lumen\Elasticsearch\Search\Sort;

/**
 * Class LocationRepository
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.05.2020
 */
class LocationRepository
{
    public const DEFAULT_LIMIT = 10;
    public const DEFAULT_OFFSET = 0;

    /**
     * Elastic Search Client
     *
     * @var ElasticsearchServiceContract
     */
    private $elastic;

    public function __construct(ElasticsearchServiceContract $elastic)
    {
        $this->elastic = $elastic;
    }

    /**
     * Elastic search index
     *
     * @return string
     */
    protected function getIndex(): string
    {
        return 'locations';
    }

    /**
     * Eloquent model
     *
     * @return string
     */
    protected function getModel(): string
    {
        return Church::class;
    }

    public function findByText(
        string $text,
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection {
        $query = (new BoolQuery())->addMust(new QueryStringTerm($text));
        $search = $this->elastic->createSearch()
            ->setIndex($this->getIndex())
            ->setSize($limit)
            ->setFrom($offset)
            ->setQuery($query);

        return $this->toCollection($this->elastic->execute($search));
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection|Church[]
     */
    public function findNearBy(
        float $latitude,
        float $longitude,
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection {
        $distanceSort = new GeoDistanceSort();
        $distanceSort->setField('location')
            ->setLocation($latitude, $longitude)
            ->setUnit(GeoDistanceSort::UNIT_KILOMETER)
            ->setOrder(GeoDistanceSort::ORDER_ASC);
        $query = $this->elastic->createSearch()
            ->setIndex($this->getIndex())
            ->setSize($limit)
            ->setFrom($offset)
            ->setSort(new Sort([$distanceSort]));

        $response = $this->elastic->execute($query);
        $collection = $this->toCollection($response)->keyBy('id');

        // Add distance to results
        foreach ($response['hits']['hits'] as $hit) {
            $model = $collection[$hit['_id']];
            $model->distance = current($hit['sort']);
        }

        return $collection->values();
    }

    protected function toCollection(array $response)
    {
        $results = collect($response['hits']['hits']);

        $keys = $results->pluck('_id')->values()->all();
        $modelClass = $this->getModel();
        /** @var Model|Builder $model */
        $model = new $modelClass();

        $models = $model->whereIn($model->getKeyName(), $keys)
            ->get()
            ->keyBy($model->getKeyName());

        return $results->map(function ($hit) use ($models) {
            return $models[$hit['_id']];
        });
    }
}
