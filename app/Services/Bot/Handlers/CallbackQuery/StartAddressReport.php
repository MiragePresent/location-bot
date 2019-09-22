<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Models\Action;
use App\Services\Bot\Answer\AddressMarkupFactory;
use App\Services\Bot\DataType\ObjectData;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Handlers\Action\IncorrectAddressReport;
use TelegramBot\Api\Types\Update;

/**
 * Class StartAddressReport
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  25.08.2019
 */
class StartAddressReport extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    use HasObject;

    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = "report_address_mistake";

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
        $object = $this->getObject($objectId);

        $this->restoreAddressMarkup($update, $object);

        // Cancel active actions
        Action::where("user_id", $this->bot->getUser()->id)
            ->isActive()
            ->update([
                "is_canceled" => true,
                "cancel_reason" => Action::CANCEL_REASON_BY_BOT,
            ]);

        // create action for reporting a mistake
        $action = Action::create([
            "user_id" => $this->bot->getUser()->id,
            "key" => IncorrectAddressReport::ACTION_KEY,
            "description" => IncorrectAddressReport::ACTION_DESCRIPTION,
            "steps" => IncorrectAddressReport::NUMBER_OF_STEPS,
            "arguments" => ["object_id" => $objectId],
            "is_confirmed" => !IncorrectAddressReport::$requireConfirmation,
        ]);

        $handler = new IncorrectAddressReport($action, $this->bot);
        $handler->handleStage($update, 0);
    }

    /**
     * Return default address buttons
     *
     * @param Update     $update
     * @param ObjectData $object
     */
    private function restoreAddressMarkup(Update $update, ObjectData $object): void
    {
        $message = $update->getCallbackQuery()->getMessage();
        $chatId = $message->getChat()->getId();

        $this->bot->getApi()->editMessageReplyMarkup(
            $chatId,
            $message->getMessageId(),
            AddressMarkupFactory::create($object)
        );
    }
}
