<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Models\Action;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Handlers\Action\IncorrectAddressReport;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use TelegramBot\Api\Types\Update;

/**
 * Class RollbackAddressReport
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  08.09.2019
 */
class RollbackAddressReport extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    use HasObject;

    public const CALLBACK_DATA = "rollback_address_report";

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

        /** @var Action $action */
        $action = Action::whereUserId($this->bot->getUser()->id)
            ->whereKey(IncorrectAddressReport::ACTION_KEY)
            ->isActive()
            ->latest()
            ->first();

        if (!$action instanceof Action) {
            throw new \InvalidArgumentException("There is no active actions");
        }

        $objectId = $this->getObjectId($update->getCallbackQuery()->getData());
        $action->decreaseStage();

        $update->getCallbackQuery()->setData(StartAddressReport::CALLBACK_DATA . "_" . $objectId);
        $startAddressReportHandler = new StartAddressReport($this->bot);
        $startAddressReportHandler->handle($update);
    }
}
