<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Bot;
use App\Services\Bot\DataType\ObjectData;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class HasObject
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  08.09.2019
 */
trait HasObject
{
    abstract public function getBot(): Bot;
    abstract public function getCallbackData(): string;

    /**
     * Finds church object ID within callback data string
     *
     * @param string $callbackData
     *
     * @return int
     */
    private function getObjectId(string $callbackData): int
    {
        return (int) str_replace($this->getCallbackData() . "_", "", $callbackData);
    }

    /**
     * Fetch object from storage
     *
     * @param $objectId
     *
     * @return ObjectData
     */
    private function getObject($objectId): ObjectData
    {
        /** @var ObjectData $object */
        $object = Cache::remember("object_{$objectId}", ObjectData::CACHE_LIFE_TIME, function () use ($objectId) {
            return $this->getBot()->getStorage()->getObject($objectId);
        });

        if (!$object) {
            throw new NotFoundHttpException("Object {$objectId} not found");
        }

        return $object;
    }
}
