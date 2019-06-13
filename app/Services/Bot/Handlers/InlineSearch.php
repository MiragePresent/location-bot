<?php

namespace App\Services\Bot\Handlers;

use App\Models\Church;
use App\Services\Bot\Bot;
use Illuminate\Database\Eloquent\Collection;
use TelegramBot\Api\Types\Inline\QueryResult\Venue;
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
            $update->getInlineQuery()->getFrom()
        ));

        $this->bot->getApi()->answerInlineQuery(
            $update->getInlineQuery()->getId(),
            $this->getResults($update->getInlineQuery()->getQuery(), (int) $update->getInlineQuery()->getOffset()),
            Bot::CACHE_INLINE_MODE_LIFE_TIME
        );
    }

    private function getResults(string $query, int $offset): array
    {
        /** @var Collection $churches */
        $churches = Church::where('name', 'like', $query . '%')
            ->take(50)
            ->offset($offset)
            ->orderBy('name')
            ->get();

        if (!$churches->count()) {
            return [];
        }

        return $churches->map(function (Church $church) {
            return new Venue(
                $church->id,
                (float) $church->latitude,
                (float) $church->longitude,
                $church->name,
                $church->address
            );
        })->toArray();
    }
}
