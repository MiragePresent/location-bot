<?php

namespace App\Services\Bot\Tracker;

use App\Models\User;

/**
 * Bos statistics tracker
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.04.2022
 */
interface StatsTrackerInterface
{
    public const REQUEST_TYPE_UNKNOWN = 'unknown';
    /** Search church by free text */
    public const REQUEST_TYPE_FREE_TEXT = 'free_text';
    /** Search church from list */
    public const REQUEST_TYPE_FIND_BY_LIST = 'find_by_list';
    /** Search church by location */
    public const REQUEST_TYPE_FIND_BY_LOCATION = 'find_by_location';
    /** Request that shows the address  */
    public const REQUEST_TYPE_ADDRESS = 'show_address';
    /** Action without returning church address */
    public const REQUEST_TYPE_INTERFACE_INTERACTION = 'interface_interaction';
    /** Bot service request (e.g. start,help,stop) */
    public const REQUEST_TYPE_SERVICE_MESSAGE = 'service_message';
    /** Developer feature request */
    public const REQUEST_TYPE_DEV = 'dev';

    public const REQUEST_STATUS_UNKNOWN = 'unknown';
    public const REQUEST_STATUS_OK = 'ok';
    public const REQUEST_STATUS_ERROR = 'error';

    /**
     * Start request tracking
     *
     * @param User|null $user
     *
     * @return StatsTrackerInterface
     */
    public function start(User $user = null): StatsTrackerInterface;

    public function setRequestType(string $requestType): StatsTrackerInterface;

    public function setRequestStatus(string $requestStatus): StatsTrackerInterface;

    public function setSentMessage(int $sentMessagesCount): StatsTrackerInterface;

    public function increaseSentMessages(int $advance = 1): StatsTrackerInterface;

    public function setFailures(int $failures): StatsTrackerInterface;

    public function increaseFailures(int $advance = 1): StatsTrackerInterface;

    public function finish();
}
