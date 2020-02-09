<?php

namespace Test\Unit\Service\Bot\Tool;

use App\Services\Bot\Exception\UpdateParseException;
use App\Services\Bot\Tool\UpdateTree;
use TelegramBot\Api\Types\CallbackQuery;
use TelegramBot\Api\Types\Chat;
use TelegramBot\Api\Types\Location;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\User;
use PHPUnit\Framework\TestCase;
use TelegramBot\Api\Types\Message;

class UpdateTreeTest extends TestCase
{
    public function test_getting_message_from_regular_text_update()
    {
        $update = $this->createRegularUpdate();

        static::assertEquals($update->getMessage(), UpdateTree::getMessage($update));
    }

    public function test_getting_user_from_regular_text_message()
    {
        $update = $this->createRegularUpdate();

        static::assertEquals($update->getMessage()->getFrom(), UpdateTree::getUser($update));
    }

    public function test_getting_chat_from_regular_text_update()
    {
        $update = $this->createRegularUpdate();

        static::assertEquals($update->getMessage()->getChat(), UpdateTree::getChat($update));
    }

    /**
     * @dataProvider locationCheckingProvider
     *
     * @param Update $update
     * @param bool   $expected
     */
    public function test_checking_if_message_has_location(Update $update, bool $expected)
    {
        static::assertEquals($expected, UpdateTree::hasLocation($update));
    }

    public function test_getting_location_from_location_update()
    {
        $update = $this->createLocationUpdate();

        static::assertEquals($update->getMessage()->getLocation(), UpdateTree::getLocation($update));
    }

    public function test_getting_location_from_regular_update()
    {
        $update = $this->createRegularUpdate();

        static::assertNull(UpdateTree::getLocation($update));
    }

    public function locationCheckingProvider(): array
    {
        return [
            [$this->createLocationUpdate(), true],
            [$this->createRegularUpdate(), false],
        ];
    }

    public function test_getting_message_from_callback_query()
    {
        $update = $this->createCallbackQueryUpdate();

        static::assertEquals($update->getCallbackQuery()->getMessage(), UpdateTree::getMessage($update));
    }

    public function test_getting_user_from_callback_query()
    {
        $update = $this->createCallbackQueryUpdate();

        static::assertEquals($update->getCallbackQuery()->getFrom(), UpdateTree::getUser($update));
    }

    public function test_getting_chat_from_callback_query()
    {
        $update = $this->createCallbackQueryUpdate();

        static::assertEquals($update->getCallbackQuery()->getMessage()->getChat(), UpdateTree::getChat($update));
    }


    /**
     * @dataProvider updateWithoutMessageDataProvider
     *
     * @param Update $update Test subject
     *
     * @throws UpdateParseException
     */
    public function test_calling_getMessage_method_with_invalid_update(Update $update)
    {
        static::expectException(UpdateParseException::class);

        UpdateTree::getMessage($update);
    }

    public function updateWithoutMessageDataProvider(): array
    {
        $regularUpdate = new Update();
        $callbackQueryUpdate = new Update();
        $callbackQueryUpdate->setCallbackQuery(new CallbackQuery());

        return [
            [$regularUpdate],
            [$callbackQueryUpdate]
        ];
    }

    /**
     * @dataProvider updateWithoutChatDataProvider
     *
     * @param Update $update
     *
     * @throws UpdateParseException
     */
    public function test_getting_chat_from_invalid_update(Update $update)
    {
        static::expectException(UpdateParseException::class);

        UpdateTree::getChat($update);
    }

    public function updateWithoutChatDataProvider(): array
    {
        $regularUpdateWithoutMessage = new Update();
        $callbackQueryUpdateWithoutMessage = new Update();
        $callbackQueryUpdateWithoutMessage->setCallbackQuery(new CallbackQuery());
        $regularUpdateWithoutChat = new Update();
        $regularUpdateWithoutChat->setMessage(new Message());
        $callbackQuery = new CallbackQuery();
        $callbackQuery->setMessage(new Message());
        $callbackQueryUpdateWithoutChat = new Update();
        $callbackQueryUpdateWithoutChat->setCallbackQuery($callbackQuery);

        return [
            [$regularUpdateWithoutMessage],
            [$regularUpdateWithoutChat],
            [$callbackQueryUpdateWithoutMessage],
            [$callbackQueryUpdateWithoutChat],
        ];
    }

    /**
     * @dataProvider updateWithoutUserDataProvider
     *
     * @param Update $update
     *
     * @throws UpdateParseException
     */
    public function test_getting_user_from_invalid_update(Update $update)
    {
        static::expectException(UpdateParseException::class);

        UpdateTree::getUser($update);
    }

    public function updateWithoutUserDataProvider(): array
    {
        $regularUpdate = new Update();
        $callbackQueryUpdate = new Update();
        $callbackQueryUpdate->setCallbackQuery(new CallbackQuery());

        return [
            [$regularUpdate],
            [$callbackQueryUpdate]
        ];
    }

    private function createRegularUpdate(): Update
    {
        $update = new Update();
        $update->setMessage($this->createMessage());

        return $update;
    }

    private function createLocationUpdate(): Update
    {
        $message = new Message();
        $message->setMessageId(1);
        $message->setFrom($this->createUser());
        $message->setChat($this->createChat());
        $message->setLocation($this->createLocation());

        $update = new Update();
        $update->setMessage($message);

        return $update;
    }

    private function createCallbackQueryUpdate(): Update
    {
        $callback = new CallbackQuery();
        $callback->setId(1);
        $callback->setData('callback_id');
        $callback->setFrom($this->createUser());
        $callback->setMessage($this->createMessage());

        $update = new Update();
        $update->setCallbackQuery($callback);

        return $update;
    }

    private function createMessage(): Message
    {
        $message = new Message();
        $message->setMessageId(1);
        $message->setText('some random text as message');
        $message->setFrom($this->createUser());
        $message->setChat($this->createChat());

        return $message;
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setId(1);
        $user->setUsername('fake_username');

        return $user;
    }

    private function createChat(): Chat
    {
        $chat = new Chat();
        $chat->setId(1);
        $chat->setTitle('Fake chat');

        return $chat;
    }

    private function createLocation(): Location
    {
        $location = new Location();
        $location->setLatitude(50.757405809);
        $location->setLongitude(23.59893);

        return $location;
    }
}
