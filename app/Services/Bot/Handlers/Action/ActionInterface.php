<?php

namespace App\Services\Bot\Handlers\Action;

use App\Models\Action;
use App\Services\Bot\Bot;
use TelegramBot\Api\Types\Update;

/**
 * Actions is temporary available events that allows user
 *
 * @package App\Services\Bot\Handlers
 */
interface ActionInterface
{
    /**
     * Action key (identity)
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Number of action steps
     *
     * @return int
     */
    public function getSteps(): int;

    /**
     * Current step number (number of finished steps)
     *
     * @return int
     */
    public function getStage(): int;

    /**
     * Action model
     *
     * @return Action
     */
    public function getModel(): Action;

    /**
     * Bot service instance
     *
     * @return Bot
     */
    public function getBot(): Bot;

    /**
     * Runs action stage handler
     *
     * @param Update $update Telegram update object
     * @param int    $stage Number os stage that has to be processed
     *
     * @return void
     */
    public function handleStage(Update $update, int $stage): void;
}
