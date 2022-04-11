<?php

namespace App\Services\Bot\Tracker;

use App\Models\Statistics;
use App\Models\User;
use Psr\Log\LoggerInterface;

/**
 * Class DBStatsTracker
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.04.2022
 */
class DBStatsTracker implements StatsTrackerInterface
{
    protected ?Statistics $statsEntry = null;

    public function __construct(protected LoggerInterface $logger) {}

    public function start(User $user = null): StatsTrackerInterface
    {
        $this->statsEntry = new Statistics();
        $this->statsEntry->request_type = static::REQUEST_TYPE_UNKNOWN;
        $this->statsEntry->request_status = static::REQUEST_STATUS_UNKNOWN;
        $this->statsEntry->sent_messages = 0;
        $this->statsEntry->failures = 0;

        return $this;
    }

    public function setRequestType(string $requestType): StatsTrackerInterface
    {
        $this->statsEntry->request_type = $requestType;

        return $this;
    }

    public function setRequestStatus(string $requestStatus): StatsTrackerInterface
    {
        $this->statsEntry->request_status = $requestStatus;

        return $this;
    }

    public function setSentMessage(int $sentMessagesCount): StatsTrackerInterface
    {
        $this->statsEntry->sent_messages = $sentMessagesCount;

        return $this;
    }

    public function increaseSentMessages(int $advance = 1): StatsTrackerInterface
    {
        $this->statsEntry->sent_messages = $this->statsEntry->sent_messages + $advance;

        return $this;
    }

    public function setFailures(int $failures): StatsTrackerInterface
    {
        $this->statsEntry->failures = $failures;

        return $this;
    }

    public function increaseFailures(int $advance = 1): StatsTrackerInterface
    {
        $this->statsEntry->failures = $this->statsEntry->failures + $advance;

        return $this;
    }

    public function finish()
    {
        try {
            $this->statsEntry->save();
        } catch (\Exception $e) {
            $this->logger->warning(sprintf(
                "Cannot save statistics message because of error: %s. \nContext: %s",
                $e->getMessage(),
                json_encode(['stats_entry' => $this->statsEntry?->toArray()])
            ));
        }

    }
}
