<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Models\Church;
use App\Services\Bot\Answer\AddressMarkupFactory;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use App\Services\SdaStorage\DataType\ObjectData;
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
    use HasObject;

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
        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_INTERFACE_INTERACTION);

        $objectId = (int) $this->getObjectId($update->getCallbackQuery()->getData());

        /** @var ObjectData $object */
        $object = Cache::remember("object_{$objectId}", ObjectData::CACHE_LIFE_TIME, function () use ($objectId) {
            return $this->bot->getStorage()->getObject($objectId);
        });

        if (!$object instanceof ObjectData) {
            throw new NotFoundHttpException("Object {$objectId} not found");
        }

        $message = $update->getCallbackQuery()->getMessage();
        $chatId = $message->getChat()->getId();

        /** @var Church $church */
        $church = Church::query()->where('object_id', $objectId)->first();

        if (!$church) {
            $this->getBot()->log("Cannot find church by object_id", "error", ["object_id" => $objectId]);

            return;
        }

        $this->bot->getApi()->editMessageReplyMarkup(
            $chatId,
            $message->getMessageId(),
            AddressMarkupFactory::create($church)
        );
    }
}
