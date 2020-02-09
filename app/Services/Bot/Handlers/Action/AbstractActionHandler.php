<?php

namespace App\Services\Bot\Handlers\Action;

use App\Models\Action;
use App\Services\Bot\Bot;
use App\Services\Bot\Tool\UpdateTree;
use Exception;
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
            $this->getModel()->done();
            $this->getBot()->log(sprintf("Action %s [stage: %d] is done", $this->getKey(), $stage));

            return;
        }

        $handlerFunc = $this->getStageHandler($stage);

        if (!is_callable($handlerFunc)) {
            throw new Exception("Action {$this->getKey()} stage[{$stage}] not implemented");
        }

        $this->getBot()->log(sprintf("Starting handling action %s [stage: %d]", $this->getKey(), $stage));

        // log activity
        $this->getModel()->activities()->create([
            "stage" => $stage,
            "data" => ["text" => UpdateTree::getMessage($update)->getText()],
        ]);

        call_user_func($handlerFunc, $this->getBot(), $update, $stage);
    }

    /**
     * Returns stage handler
     *
     * @param int $stage
     *
     * @return callable
     */
    abstract protected function getStageHandler(int $stage);
}
