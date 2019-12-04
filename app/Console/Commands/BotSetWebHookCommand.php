<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use TelegramBot\Api\BotApi;

/**
 * Class BotSetWebHookCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  01.12.2019
 */
class BotSetWebHookCommand extends Command
{
    /**
     * The command signature used for calling it from console
     *
     * @var string
     */
    protected $signature = "bot:set-webhook {url}";

    /**
     * @var string
     */
    protected $description = "Set bot web hook handler url";

    /**
     * Calls Telegram bot api and sets new handler url (passed as command's argument)
     *
     * @param BotApi $botApi
     *
     * @throws \TelegramBot\Api\Exception
     * @throws \TelegramBot\Api\InvalidArgumentException
     */
    public function handle(BotApi $botApi)
    {
        $url = $this->argument('url');
        $validator = Validator::make($this->arguments(), ['url' => 'required|url|regex:/^https.*/s']);

        if ($validator->fails()) {
            $this->error("Url is not valid. Please, check it and try again");
            return;
        }

        $response = $botApi->setWebhook($url);

        if ($response != 1) {
            $this->error(sprintf(
                "URL has not been set! \n" .
                "Telegram API response: \n%s",
                $response
            ));

            return;
        }

        $botInfo = $botApi->getMe();
        $this->comment("Success!");
        $this->info(sprintf("The @%s bot handler set to %s", $botInfo->getUsername(), $url));
    }
}
