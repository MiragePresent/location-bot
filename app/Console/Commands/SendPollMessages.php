<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PollAnswer;
use App\Services\Bot\Answer\AskFeedbackMessage;
use App\Services\Bot\Bot;
use App\Services\Bot\UserPoll;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SendPollMessages extends Command
{

    protected $signature = 'poll:send-messages ' .
        '{poll : Pool name that are going to be sent} ' .
        '{--user= : Username or user ID to send the poll to}';

    protected $description = 'Send pool messages to all the users';

    public function handle(Bot $bot) {
        $pollName = UserPoll::tryFrom($this->argument('poll'));
        $userId = $this->option('user');

        if (empty($pollName)) {
            $polls = implode(',', array_map(fn(UserPoll $pollName) => $pollName->value, UserPoll::cases()));
            $this->error("Invalid poll name!");
            $this->line("Poll name must be one of the following: " . $polls);

            return Command::FAILURE;
        }

        $bot->getLogger()->info("Send messages command received. Poll: " . $pollName->value . ", User: " . $userId);

        if (!empty($userId)) {
            /** @var null|User $user */
            $user = User::query()
                ->where('id', $userId)
                ->orWhere('username', $userId)
                ->first();

            if (!$user) {
                $this->error(sprintf("User '%s' not found", $userId));
                return Command::FAILURE;
            }

            return $this->sendPollToUser($bot, $pollName, $user);
        }

        $result = User::chunk(100, fn($users) => $this->sendPoll($bot, $pollName, $users));

        return $result
            ? Command::SUCCESS
            : Command::FAILURE;
    }

    protected function sendPollToUser(Bot $bot, UserPoll $pollName, User $user): int
    {
        if (!$user->chat_id) {
            $bot->getLogger()->warning("User " . $user->username . " chat id is empty");
            $this->warn("Can't find chat_id for user " . $user->id);
            return Command::SUCCESS;
        }
        if (UserPoll::BasicFeedback === $pollName) {
            return $this->sendBasicAskFeedback($bot, $user);
        }
        return Command::SUCCESS;
    }

    protected function sendPoll(Bot $bot, UserPoll $pollName, Collection $users): bool
    {
        foreach ($users as $user) {
            $userRes = $this->sendPollToUser($bot, $pollName, $user);

            if (!$userRes) {
                return false;
            }
        }

        return true;
    }

    protected function sendBasicAskFeedback(Bot $bot, User $user): int
    {
        $sentRecently = PollAnswer::query()
            ->where("poll_name", UserPoll::BasicFeedback->value)
            ->where("answer", "message_sent")
            ->where("user_id", $user->id)
            ->where("created_at", ">", DB::raw("CURRENT_TIMESTAMP - interval '15 minutes'"))
            ->exists();

        if ($sentRecently) {
            $bot->getLogger()->info("Feedback request has been sent recently. User: " . $user->username);
            
            return Command::SUCCESS;
        }

        $bot->getLogger()->info("Sending basic feedback request message to user: " . $user->username);
        $bot->getStatsTracker()->start($user);
        $bot->sendTo($user->chat_id, new AskFeedbackMessage());

        $userPoll = new PollAnswer();
        $userPoll->user_id = $user->id;
        $userPoll->poll_name = UserPoll::BasicFeedback->value;
        $userPoll->answer = "message_sent";
        $userPoll->save();

        return Command::SUCCESS;
    }
}
