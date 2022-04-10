<?php

namespace App\Services\Bot\Handlers\KeyboardReply;

use App\Models\Church;
use App\Repository\LocationRepository;
use App\Services\Bot\Answer\SelectOptionAnswer;
use App\Services\Bot\Handlers\AbstractUpdateHandler;
use App\Services\Bot\Tool\UpdateTree;
use Exception;
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
class DefaultTextReplyHandler extends AbstractUpdateHandler implements KeyboardReplyHandlerInterface
{
    /**
     * @inheritDoc
     */
    public static function isSuitable(Message $message): bool
    {
        // THIS HANDLER MUST HAVE THE SMALLEST PRIORITY
        return true;
    }

    /**
     * @inheritDoc
     */
    public function handle(Update $update, LocationRepository $repository): void
    {
        $this->bot->log(sprintf(
            "Show addresses in city: %s \nFor: %s",
            $update->getMessage()->getText(),
            $update->getMessage()->getFrom()->toJson()
        ));

        $message = UpdateTree::getMessage($update);
        $chat = UpdateTree::getChat($update);

        /** @var Church[]|Collection $churches */
        $churches = Cache::remember(
            md5("churches_" . $message->getText()),
            Church::CACHE_LIFE_TIME,
            function () use ($repository, $message) {
                /** @var Collection|Church[] $churches */
                $churches = Church::whereHas('city', function ($sql) use ($message) {
                    $sql->where('name', $message->getText());
                })->orderBy('name')->get();

                return $churches->count() === 0
                    ? $repository->findByText($message->getText())
                    : $churches;
            }
        );

        $this->getBot()->log("Found churches", 'info', $churches->toArray());

        if ($churches->count() === 0) {
            throw new Exception('No locations found.');
        }

        if ($churches->count() === 1) {
            $update->getMessage()->setText($churches->first()->name);
            ShowAddressReply::dispatch($this->bot, $update);

            return;
        }

        $keyboardOptions = $churches->map(function (Church $church) {
            return [[ "text" => $church->name ]];
        })->values()->toArray();

        $answer = new SelectOptionAnswer(
            trans("bot.messages.text.specify_a_church", ["query" => $message->getText()]),
            $keyboardOptions
        );

        $this->getBot()->sendTo($chat->getId(), $answer);
    }
}
