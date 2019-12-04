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
    public function webHookCallback(Bot $bot, Request $request)
    {
        try {
            $update = new Update();
            $update->map($request->all());

            $bot->processUpdate($update);
            $bot->run();
        } catch (Exception $e) {
            Log::error(date("[Y-m-d H:i:s] >> ") . $e->getMessage(), $e->getTrace());
        }
    }
}
