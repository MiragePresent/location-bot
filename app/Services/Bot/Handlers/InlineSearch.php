<?php

namespace App\Services\Bot\Handlers;

use App\Models\Church;
use App\Repository\LocationRepository;
use App\Services\Bot\Answer\AddressAnswer;
use App\Services\Bot\Bot;
use App\Services\SdaStorage\DataType\ObjectData;
use App\Services\SdaStorage\StorageClient;
use Exception;
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
    /**
     * Count results per request in bot search result
     *
     * @var int
     */
    private const COUNT_RESULTS_PER_PAGE = 10;

    public function handle(Update $update, LocationRepository $repository, StorageClient $storage): void
    {
        $this->bot->log(sprintf(
            "Inline query request: %s\nFor: %s",
            $update->getInlineQuery()->getQuery(),
            $update->getInlineQuery()->getFrom()->toJson()
        ));

        if ($update->getInlineQuery()->getQuery() === '') {
            return;
        }

        $churches = $repository->findByText(
            $update->getInlineQuery()->getQuery(),
            self::COUNT_RESULTS_PER_PAGE,
            (int) $update->getInlineQuery()->getOffset()
        );

        if ($churches->count() <=0 ) {
            $this->getBot()->log("Nothing found '{$update->getInlineQuery()->getQuery()}'");
            return;
        }

        $results = $churches->map(function (Church $church) use ($storage) {
            /** @var ObjectData $object */
            $object = $storage->getObject($church->object_id);
            $result = new AddressAnswer($object);

            return new Article(
                (string) $object->id,
                $church->name,
                $church->address,
                $object->photo ? $object->photo->url : null,
                $object->photo ? $object->photo->width : null,
                $object->photo ? $object->photo->height : null,
                new InputMessageContent\Text($result->getText(), Bot::PARSE_FORMAT_MARKDOWN),
                $result->getMarkup()
            );
        })->all();

        try {
            $this->bot->getApi()->answerInlineQuery(
                $update->getInlineQuery()->getId(),
                $results,1
//                Bot::CACHE_INLINE_MODE_LIFE_TIME
            );
        } catch (Exception $exception) {
            $this->getBot()->log($exception->getMessage());
        }
    }
}
