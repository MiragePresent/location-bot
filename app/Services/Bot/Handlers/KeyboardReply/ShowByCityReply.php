<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\Church;
use App\Services\Bot\Answer\SelectOptionAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tool\UpdateTree;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\Message;

/**
 * Class ShowByCityReply
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.06.2019
 */
class ShowByCityReply extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        return Cache::remember(
            md5('query_' . $message->getText() . '_status'),
            Church::CACHE_LIFE_TIME,
            function () use ($message) {
                return Church::search($message->getText())->get()->count() > 0;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update): void
    {
        $this->bot->log(sprintf(
            "Show addresses in city: %s \nFor: %s",
            $update->getMessage()->getText(),
            $update->getMessage()->getFrom()->toJson()
        ));

        $message = UpdateTree::getMessage($update);

        /** @var Church[]|Collection $churches */
        $churches = Cache::remember(
            md5("churches_" . $message->getText()),
            Church::CACHE_LIFE_TIME,
            function () use ($message) {
                return Church::search($message->getText())
                    ->orderBy('name')
                    ->get();
            }
        );

        if ($churches->count() === 1) {
            $update->getMessage()->setText($churches->first()->name);
            ShowAddressReply::dispatch($this->bot, $update);

            return;
        }

        $churches = $churches->map(function (Church $church) {
            return [[ "text" => $church->name ]];
        })->toArray();
        $answer = new SelectOptionAnswer(trans("bot.messages.text.specify_a_church"), $churches);

        $this->bot->sendTo($update->getMessage(), $answer);
    }
}
