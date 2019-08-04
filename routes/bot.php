<?php
/**
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  07.06.2019
 */

/** @var $router \Laravel\Lumen\Routing\Router */
$router->post("/", ['uses' => 'BotController@webHookCallback']);

$router->get('/setwebhook', ['uses' => 'BotController@setWebHook']);
