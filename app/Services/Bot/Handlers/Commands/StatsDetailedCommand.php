<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Models\Statistics;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Handlers\KeyboardReply\IncorrectMessage;
use App\Services\Bot\Tool\UpdateTree;
use App\Services\Bot\Tracker\StatsRepositoryInterface;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class StatsCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.04.2022
 */
class StatsDetailedCommand extends StatsCommand
{
    public const COMMAND_SIGNATURE = 'stats_detailed';

    /**
     * @inheritDoc
     */
    public function getSignature(): string
    {
        return static::COMMAND_SIGNATURE;
    }

    /**
     * @inheritDoc
     */
    public function handle(Message $message): void
    {
        parent::handle($message);

        // by request type
        /** @var Collection $requestTypes */
        $requestTypes = Statistics::query()
            ->selectRaw('distinct(request_type) request_type')
            ->get()
            ->pluck('request_type');

        $report = $requestTypes->map(function (string $requestType) {
            $countFailures = Statistics::query()
                ->selectRaw('sum(failures) as count')
                ->where('request_type', $requestType)
                ->first()
                ->count();
            $countMessages = Statistics::query()
                ->selectRaw('sum(sent_messages) as count')
                ->where('request_type', $requestType)
                ->first()
                ->count;

            return [
                'request_type' => $requestType,
                'failures' => $countFailures,
                'messages' => $countMessages,
            ];
        })
        ->reduce(
            fn ($item) => sprintf(
                "**%s**: %d message(s), %d error(s) \n",
                $item['request_type'],
                $item['messages'],
                $item['failures'],
            ),
            ""
        );

        $this->bot->sendTo(UpdateTree::getChat($message)->getId(), new TextAnswer($report));
    }
}
