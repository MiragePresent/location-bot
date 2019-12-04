<?php

namespace App\Services\Bot\Handlers\Action;

use App\Models\Action;
use App\Services\Bot\Bot;
use Exception;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class AddressReportAction
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  13.08.2019
 */
abstract class AbstractActionHandler implements ActionInterface
{
    /**
     * Action has to be confirmed
     *
     * @var bool
     */
    public static $requireConfirmation = true;

    /**
     * Bot service instance
     *
     * @var Bot
     */
    protected $bot;

    /**
     * Action model
     *
     * @var Action
     */
    protected $action;

    public function __construct(Action $action, Bot $bot)
    {
        $this->action = $action;
        $this->bot = $bot;
    }

    /**
     * @inheritDoc
     */
    public function getStage(): int
    {
        return $this->getModel()->stage;
    }

    /**
     * @inheritDoc
     */
    public function getBot(): Bot
    {
        return $this->bot;
    }

    /**
     * @inheritDoc
     */
    public function getModel(): Action
    {
        return $this->action;
    }

    /**
     * @inheritDoc
     */
    public function handleStage(Update $update, int $stage): void
    {
        if ($stage >= $this->getSteps()) {
            if ($this::$requireConfirmation) {
                if ($this instanceof ConfirmableActionInterface) {
                    $this->sendConfirmationMessage($update->getMessage());
                } else {
                    throw new Exception("Action has to implement Confimable");
                }
            } else {
                $this->getModel()->done();
                $this->getBot()->log(sprintf("Action %s [stage: %d] is done", $this->getKey(), $stage));
            }

            return;
        }

        $handlerFunc = $this->getStageHandler($stage);
        $this->getBot()->log(sprintf("Starting handling action %s [stage: %d]", $this->getKey(), $stage));

        /** @var Message|null $message */
        $message = $update->getMessage();

        if (!$message instanceof Message) {
            $message = $update->getCallbackQuery()->getMessage();
        }

        if (!is_callable($handlerFunc)) {
            throw new Exception("Action {$this->getKey()} stage[{$stage}] not implemented");
        }

        // log activity
        $this->getModel()->activities()->create([
            "stage" => $stage,
            "data" => ["text" => $message->getText()],
        ]);

        call_user_func($handlerFunc, $this->getBot(), $update, $stage);
        $this->getBot()->log(sprintf("Action %s - stage: %d is processed", $this->getKey(), $stage));

        $this->getModel()->increaseStage();
        $this->getBot()->log(sprintf("Action %s [stage: %d] is completed", $this->getKey(), $stage));
    }

    /**
     * Returns stage handler
     *
     * @param int $stage
     *
     * @return callable
     */
    abstract protected function getStageHandler(int $stage): callable;
}
