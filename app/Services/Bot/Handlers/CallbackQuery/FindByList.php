<?php

namespace App\Services\Bot\Handlers\CallbackQuery;

use App\Models\Region;
use App\Services\Bot\Answer\SelectOptionAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use TelegramBot\Api\Types\Update;

/**
 * Class FindByList
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.06.2019
 */
class FindByList extends AbstractUpdateHandler implements CallbackQueryHandlerInterface
{
    /**
     * Callback identity name
     *
     * @var string
     */
    public const CALLBACK_DATA = "search_by_list";

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
    public function handle(Update $update): void
    {
        $this->bot->log(
            sprintf(
                "CallbackQuery: %s \nFrom: %s",
                $update->getCallbackQuery()->getData(),
                $update->getCallbackQuery()->getFrom()->toJson()
            )
        );

        /** @var Region[]|Collection $regions */
        $regions = Cache::remember(
            'regions',
            Region::CACHE_LIFE_TIME,
            function () {
                return Region::orderBy('name')->get();
            }
        )->map(function (Region $region) {
            return [["text" => $region->name ]];
        })->toArray();

        $answer = new SelectOptionAnswer(trans("bot.messages.text.find_by_list"), $regions);

        $this->bot->sendTo($update->getCallbackQuery()->getMessage()->getChat()->getId(), $answer);
    }
}
