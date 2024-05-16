<?php

namespace App\Services\Bot\Handlers\Commands;

use App\Models\PollAnswer;
use App\Services\Bot\Answer\TextAnswer;
use App\Services\Bot\Handlers\AbstractCommandHandler;
use App\Services\Bot\Handlers\KeyboardReply\IncorrectMessage;
use App\Services\Bot\Tracker\StatsRepositoryInterface;
use App\Services\Bot\Tracker\StatsTrackerInterface;
use App\Services\Bot\UserPoll;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

/**
 * Class StatsCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  10.04.2022
 */
class StatsCommand extends AbstractCommandHandler
{
    public const COMMAND_SIGNATURE = 'stats';

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
        if (!$this->bot->getUser()->isAdmin()) {
            $update = new Update();
            $update->setMessage($message);

            IncorrectMessage::dispatch($this->bot, $update);

            return;
        }

        $this->bot->getStatsTracker()->setRequestType(StatsTrackerInterface::REQUEST_TYPE_DEV);

        /** @var StatsRepositoryInterface $statsRepository */
        $statsRepository = App::make(StatsRepositoryInterface::class);
        $chatId = $message->getChat()->getId();

        $totalFeedback = PollAnswer::query()
            ->where('answer', '!=', 'message_sent')
            ->count();
        $totalSent = PollAnswer::query()->where('answer', 'message_sent')->count();
        $positiveFeedback = PollAnswer::query()->where('answer', 'vote_up')->count();
        $negativeFeedback = PollAnswer::query()->where('answer', 'vote_down')->count();
        $uniqueVotes = PollAnswer::query()->selectRaw('DISTINCT user_id as count')
            ->where('answer', '!=', 'message_sent')
            ->first()
            ->count ?: 0;
        
        $votesTable = sprintf("Poll: %s\n", UserPoll::BasicFeedback->value);

        $basicFeedback = PollAnswer::query()
            ->select(
                'answer',
                DB::raw('count(user_id) as votes')
            )
            ->where('poll_name', UserPoll::BasicFeedback->value)
            ->groupBy('answer')
            ->orderBy(DB::raw('count(user_id)'), 'desc')
            ->get();
        
        foreach ($basicFeedback as $feedbackAnswer) {
            $votesTable .= sprintf("%s : %d\n", str_replace("_", "\_", $feedbackAnswer->answer), $feedbackAnswer->votes);
        }

        $votesTable .= sprintf("\nPoll: %s\n", UserPoll::DetailedFeedback->value);
        $detailedFeedback = PollAnswer::query()
            ->select(
                'answer',
                DB::raw('count(user_id) as votes')
            )
            ->where('poll_name', UserPoll::DetailedFeedback->value)
            ->groupBy('answer')
            ->orderBy(DB::raw('count(user_id)'), 'desc')
            ->get();
        
        foreach ($detailedFeedback as $feedbackAnswer) {
            $votesTable .= sprintf("%s : %d\n", str_replace("_", "\_", $feedbackAnswer->answer), $feedbackAnswer->votes);
        }

        $generalStats = new TextAnswer(sprintf(
            "*General statistics*\n" .
            "Number of users: %d\n" .
            "Requests number (_since 11.04.2022_): %d\n" .
            "Number of churches found: %d\n" .
            "Errors: %d\n\n" .
            "Humanitarian help requests (_since 15.06.2022_): %d\n\n" . 
            "Feedback answers: %d/%d (unique: %d)\n" . 
            "Positive: %d\n" . 
            "Negative: %d\n\n" . 
            "%s",
            $statsRepository->numberOfUsers(),
            $statsRepository->numberOfAllRequests(),
            $statsRepository->numberOfAddressesFound(),
            $statsRepository->numberOfErrors(),
            $statsRepository->numberOfHumanitarianHelpRequests(),
            $totalFeedback, $totalSent, $uniqueVotes,
            $positiveFeedback,
            $negativeFeedback,
            $votesTable,
        ));

        $this->bot->sendTo($chatId, $generalStats);

        // TODO: detailed statistics message
    }
}
