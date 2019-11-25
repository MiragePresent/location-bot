<?php

namespace App\Services\Bot\Handlers;

use App\Models\Church;
use App\Services\Bot\Answer\AddressAnswer;
use App\Services\Bot\Bot;
use App\Services\Bot\DataType\ObjectData;
use Illuminate\Database\Eloquent\Collection;
use TelegramBot\Api\Types\Inline\InputMessageContent;
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

        if ($update->getInlineQuery()->getQuery() === '') {
            return;
        }

        $results = $this->getResults(
            $update->getInlineQuery()->getQuery(),
            (int) $update->getInlineQuery()->getOffset()
        );

        if (count($results) > 0) {
            $results[] = new Article(
                'algolia-id',
                'Search by Algolia',
                'A powerful hosted search API that provides product teams with the resources ' .
                '& tools they need to create fast, relevant search.',
                'https://cdn4.iconfinder.com/data/icons/logos-and-brands/512/12_Algolia_logo_logos-512.png',
                null,
                null,
                new InputMessageContent\Text(
                    '*Search by Algolia*' . PHP_EOL .
                    'A powerful hosted search API that provides product teams with the resources ' .
                    '& tools they need to create fast, relevant search.',
                    Bot::PARSE_FORMAT_MARKDOWN
                )
            );
        }

        try {
            $this->bot->getApi()->answerInlineQuery(
                $update->getInlineQuery()->getId(),
                $results,
                Bot::CACHE_INLINE_MODE_LIFE_TIME
            );
        } catch (\Exception $exception) {
            $this->getBot()->log($exception->getMessage());
        }
    }

    private function getResults(string $query, int $offset): array
    {
        /** @var array|Church[]|Collection $churches */
        $churches = Church::search($query)
            ->get();

        if ($churches->count() <=0 ) {
            $this->getBot()->log("Nothing found '{$query}'");
            return [];
        }

        return $churches->map(function (Church $church) {
            /** @var ObjectData $object */
            $object = $this->bot->getStorage()->getObject($church->object_id);
            $result = new AddressAnswer($object);

            return new Article(
                (string) $object->id,
                $object->getName(),
                $object->getAddress(),
                $object->photo ? $object->photo->url : null,
                $object->photo ? $object->photo->width : null,
                $object->photo ? $object->photo->height : null,
                new InputMessageContent\Text($result->getText(), Bot::PARSE_FORMAT_MARKDOWN),
                $result->getMarkup()
            );
        })->all();
    }
}
