<?php

namespace App\Repository;

use App\Models\Church;
use App\Services\Elastic\GeoDistanceSort;
use App\Services\Elastic\QueryStringTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
     * @param int   $radius     Search radius in meters
     * @param float $latitude
     * @param float $longitude
     * @param int   $limit
     * @param int   $offset
     *
     * @return Collection|Church[]
     */
    public function findNearBy(
        int $radius,
        float $latitude,
        float $longitude,
        int $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ): Collection {
        return Church::query()
            ->addSelect([
                "*",
                DB::raw("ROUND(earth_distance(ll_to_earth(?,?), ll_to_earth(latitude, longitude))::NUMERIC, 3) AS distance")
            ])
            ->where(function ($query) use ($radius, $latitude, $longitude){
                $query
                    ->whereRaw("earth_box(ll_to_earth(?,?), ?) @> ll_to_earth(latitude, longitude)")
                    ->whereRaw("earth_distance(ll_to_earth(?,?), ll_to_earth(latitude, longitude)) < ?");
            })
            ->addBinding([
                $latitude, $longitude,
                $latitude, $longitude, $radius,
                $latitude, $longitude, $radius,
            ])
            ->orderBy("distance")
            ->limit($limit)
            ->offset($offset)
            ->get();
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
