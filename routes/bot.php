<?php
/**
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.06.2019
 */

/** @var $router \Laravel\Lumen\Routing\Router */
$router->post("/", ['uses' => 'BotController@webHookCallback']);

$router->get('/setwebhook', function (\App\Services\Bot\Bot $bot) {
    $ngrok = "https://ac5758d1.ngrok.io";

    $response = $bot->getApi()->setWebhook($ngrok . "/bot");

    echo $response;
});
