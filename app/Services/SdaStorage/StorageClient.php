<?php

namespace App\Services\SdaStorage;

use App\Services\SdaStorage\DataType\ObjectData;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

/**
 * Class StorageClient
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  14.06.2019
 */
class StorageClient
{
    /**
     * Storage url
     *
     * @var string
     */
    protected $url;

    /**
     * Http Client
     *
     * @var ClientInterface
     */
    protected $http;

    /**
     * StorageClient constructor.
     *
     * @param string          $url
     * @param ClientInterface $http
     */
    public function __construct(string $url, ClientInterface $http)
    {
        $this->url = $url;
        $this->http = $http;
    }

    /**
     * Load objects from API
     * Call API for fetching churches data
     *
     * @param int $offset Fetch offset (next item number)
     * @param int $limit Fetching list limit
     *
     * @return array<int,ObjectData>
     * @throws GuzzleException
     */
    public function getObjects(int $offset, int $limit): array
    {
        $result = [];
        $response = $this->http->request("GET", "{$this->url}/objects?offset={$offset}&limit={$limit}");

        if ($response->getStatusCode() === 200) {
            $json = $response->getBody()->getContents();
            $data = json_decode($json, true);

            foreach ($data['mapObjects'] as $data) {
                $object = new ObjectData();
                $object->loadFrom($data);

                $result[] = $object;
            }
        }

        return $result;
    }

    /**
     * Returns API object data
     *
     * @param int $objectId
     * @param bool $cached
     *
     * @return ObjectData
     * @throws GuzzleException
     */
    public function getObject(int $objectId, bool $cached = true): ObjectData
    {
        if ($cached) {
            return Cache::remember("api_object_{$objectId}", ObjectData::CACHE_LIFE_TIME, function () use ($objectId) {
                return $this->fetchObject($objectId);
            });
        }

        return $this->fetchObject($objectId);
    }

    /**
     * Fetches object from API
     *
     * @param int $objectId
     *
     * @return ObjectData
     * @throws GuzzleException
     */
    protected function fetchObject(int $objectId): ?ObjectData
    {
        $response = $this->http->request("GET", "{$this->url}/objects/{$objectId}");

        if ($response->getStatusCode() === 200) {
            $json = $response->getBody()->getContents();
            $data = json_decode($json, true);

            $object = new ObjectData();

            return $object->loadFrom($data);
        }

        return null;
    }
}
