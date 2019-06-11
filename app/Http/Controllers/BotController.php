<?php

namespace App\Http\Controllers;

use App\Services\Bot\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\MessageEntity;
use TelegramBot\Api\Types\Update;

/**
 * Class BotController
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  09.06.2019
 */
class BotController extends Controller
{
    public function webHookCallback(Bot $bot, Request $request)
    {
        try {
            $update = new Update();
            $update->map($request->all());

            Log::info(date("[Y-m-d H:i:s] >> ") . $update->toJson());
//            Log::info("Chat ID: {$update->getMessage()->getChat()->getId()}");

            $bot->processUpdate($update);

            $bot->run();
        } catch (\Exception $e) {
            Log::error(date("[Y-m-d H:i:s] >> ") . $e->getMessage(), $e->getTrace());
        }
    }

    /**
     * Creates message data type from request
     *
     * @param Request $request
     *
     * @return Message
     */
    protected function createMessage(Request $request): Message
    {
        $message = new Message();
        $message->map($request->message);

        return $message;
    }

    protected function isCommand(Message $message): bool
    {
        return array_reduce($message->getEntities(), function (bool $isCommand, MessageEntity $entity) {
            return $isCommand || $entity->getType() === Bot::MESSAGE_ENTITY_TYPE_BOT_COMMAND;
        }, false);
    }
}
