<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Services\Bot\Answer\AddressMarkupFactory;
use App\Services\Bot\DataType\ObjectData;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TelegramBot\Api\Types\Update;

/**
 * Class RemoveReportButtons
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  26.08.2019
 */
class RemoveReportButtons extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = "remove_report_buttons";

    /**
     * @inheritDoc
     */
    public function getCallbackData(): string
    {
        return static::CALLBACK_DATA;
    }

    /**
     * @inheritDoc
     */
    public static function isSuitable(string $callbackData): bool
    {
        return false !== strpos($callbackData, static::CALLBACK_DATA);
    }

    public function handle(Update $update): void
    {
        $objectId = $this->getObjectId($update->getCallbackQuery()->getData());

        /** @var ObjectData $object */
        $object = Cache::remember("object_{$objectId}", ObjectData::CACHE_LIFE_TIME, function () use ($objectId) {
            return $this->bot->getStorage()->getObject($objectId);
        });

        if (!$object) {
            throw new NotFoundHttpException("Object {$objectId} not found");
        }

        $message = $update->getCallbackQuery()->getMessage();
        $chatId = $message->getChat()->getId();

        $this->bot->getApi()->editMessageReplyMarkup(
            $chatId,
            $message->getMessageId(),
            AddressMarkupFactory::create($object)
        );
    }

    /**
     * Finds church object ID within callback data string
     *
     * @param string $callbackData
     *
     * @return string
     */
    private function getObjectId(string $callbackData): string
    {
        return str_replace(static::CALLBACK_DATA . "_", "", $callbackData);
    }
}
