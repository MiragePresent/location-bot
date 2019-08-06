<?php

namespace App\Services\Bot\Handlers;

use App\Models\Church;
use App\Services\Bot\Bot;
use App\Services\Bot\DataType\ObjectData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use TelegramBot\Api\Types\Inline\QueryResult\Article;
use TelegramBot\Api\Types\Update;

/**
 * Class InlineSearch
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.06.2019
 */
class InlineSearch extends AbstractUpdateHandler
{
    public function handle(Update $update): void
    {
        $this->bot->log(sprintf(
            "Inline query request: %s\nFor: %s",
            $update->getInlineQuery()->getQuery(),
            $update->getInlineQuery()->getFrom()->toJson()
        ));

        $this->bot->getApi()->answerInlineQuery(
            $update->getInlineQuery()->getId(),
            $this->getResults($update->getInlineQuery()->getQuery(), (int) $update->getInlineQuery()->getOffset()),
            Bot::CACHE_INLINE_MODE_LIFE_TIME
        );
    }

    private function getResults(string $query, int $offset): array
    {
        /** @var Collection|Church[] $churches */
        $churches = Cache::remember(
            "search_like_{$query}_{$offset}",
            Church::CACHE_LIFE_TIME,
            function () use ($query, $offset) {
                return Church::where('name', 'like', $query . '%')
                    ->take(50)
                    ->offset($offset)
                    ->orderBy('name')
                    ->get();
            }
        );

        if (!$churches->count()) {
            return [];
        }

        return $churches->map(function (Church $church) {
            /** @var ObjectData $object */
            $object = $this->bot->getStorage()->getObject($church->object_id);

            return new Article(
                $object->id,
                $object->getName(),
                $object->getAddress(),
                $object->photo
            );
        })->toArray();
    }
}
