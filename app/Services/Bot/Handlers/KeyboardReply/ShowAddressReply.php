<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\Church;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Message\AddressMessage;
use Illuminate\Support\Facades\Cache;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * The church address message
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class ShowAddressReply extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        /** @var Church $church */
        $church = Cache::remember(
            md5($message->getText()),
            Church::CACHE_LIFE_TIME,
            function () use ($message) {
                return Church::where('name', $message->getText())->first();
            }
        );

        return !is_null($church);
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->log(sprintf(
            "Show address: %s \nFor: %s",
            $update->getMessage()->getText(),
            $update->getMessage()->getFrom()->toJson()
        ));

        /** @var Church $church */
        $church = Cache::remember(
            md5($update->getMessage()->getText()),
            Church::CACHE_LIFE_TIME,
            function () use ($update) {
                return Church::where('name', $update->getMessage()->getText())->first();
            }
        );

        $object = $this->bot->getStorage()->getObject($church->object_id);
        $message = new AddressMessage($object);

        $this->bot->reply(
            $update->getMessage()->getChat()->getId(),
            $message->getText(),
            $message->getMarkup()
        );
    }
}
