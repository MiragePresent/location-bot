<?php

namespace App\Services\Bot\Tool;

use App\Services\Bot\Exception\UpdateParseException;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\Location;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\User;

/**
 * A tool for quick search child entities inside Telegram data type
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  20.12.2019
 */
final class UpdateTree
{
    /**
     * Finds message object inside an update
     *
     * @param Update $update Telegram bot update object
     *
     * @return Message
     * @throws UpdateParseException Message not found
     */
    public static function getMessage(Update $update): Message
    {
        if ($update->getMessage() instanceof Message) {
            $message = $update->getMessage();
        } elseif ($update->getCallbackQuery() instanceof CallbackQuery
            && $update->getCallbackQuery()->getMessage() instanceof Message
        ) {
            $message = $update->getCallbackQuery()->getMessage();
        } else {
            throw new UpdateParseException('Unable to find message object inside the update');
        }

        return $message;
    }

    /**
     * Finds the telegram bot chat object inside an update
     *
     * @param Update $update Telegram bot update
     *
     * @return Chat
     * @throws UpdateParseException Chat not found
     */
    public static function getChat(Update $update): Chat
    {
        try {
            $message = self::getMessage($update);
            $chat = $message->getChat();
        } catch (UpdateParseException $e) {
            // skip previous error
        }

        if (!isset($chat) || !$chat instanceof Chat) {
            throw new UpdateParseException('Unable to find chat object inside the update');
        }

        return $chat;
    }

    /**
     * Finds user object inside an update
     *
     * @param Update $update Telegram update object
     *
     * @return User
     * @throws UpdateParseException User not found
     */
    public static function getUser(Update $update): User
    {
        if ($update->getMessage() instanceof Message && $update->getMessage()->getFrom() instanceof User) {
            $user = $update->getMessage()->getFrom();
        } elseif ($update->getCallbackQuery() instanceof CallbackQuery
            && $update->getCallbackQuery()->getFrom() instanceof User
        ) {
            $user = $update->getCallbackQuery()->getFrom();
        } else {
            throw new UpdateParseException('Unable to find user object inside the update');
        }

        return $user;
    }

    /**
     * Checks if update has location
     *
     * @param Update $update
     *
     * @return bool
     */
    public static function hasLocation(Update $update): bool
    {
        return self::getLocation($update) instanceof Location;
    }

    /**
     * Finds the location object inside an update
     *
     * @param Update $update
     *
     * @return Location|null
     */
    public static function getLocation(Update $update): ?Location
    {
        return $update->getMessage()->getLocation();
    }
}
