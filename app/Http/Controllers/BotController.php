<?php

namespace App\Http\Controllers;

use App\Services\Bot\Bot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\Types\Update;

/**
 * Class BotController
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  09.06.2019
 */
class BotController extends Controller
{
    public function setWebHook(Bot $bot, Request $request)
    {
        $url = "https://{$request->getHost()}/bot";
        $response = $bot->getApi()->setWebhook($url);

        if ($response != 1) {
            $debugInfo = [
                'Host' => $request->getHost(),
                'WebHookURL' => $url,
                'BotAPIResponse' => $response,
            ];

            throw new Exception(sprintf(
                "Web hook url cannot be set\nInfo: %s",
                json_encode($debugInfo)
            ));
        }

        echo $url . ' – ' . 'OK';
    }
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
}
