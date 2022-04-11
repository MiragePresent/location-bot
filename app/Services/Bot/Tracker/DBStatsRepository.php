<?php

namespace App\Services\Bot\Tracker;

use App\Models\Statistics;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Class DBStatsRepository
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.04.2022
 */
class DBStatsRepository implements StatsRepositoryInterface
{
    public function numberOfUsers(): int
    {
        return User::query()->count();
    }

    public function numberOfAllRequests(): int
    {
        return Statistics::query()->count();
    }

    public function numberOfAddressesFound(): int
    {
        $showAddressesMessages = DB::table('statistics')
            ->select([DB::raw('sum(sent_messages) as all_address_messages')])
            ->where('request_type', StatsTrackerInterface::REQUEST_TYPE_ADDRESS)
            ->where('request_status', StatsTrackerInterface::REQUEST_STATUS_OK)
            ->where('sent_messages', '>', 0)
            ->first()
            ->all_address_messages;

        $incorrectLocationWarnings = Statistics::query()
            ->where('request_type', StatsTrackerInterface::REQUEST_TYPE_ADDRESS)
            ->where('request_status', StatsTrackerInterface::REQUEST_STATUS_OK)
            ->where('sent_messages', '>', 1)
            ->count();


        return $showAddressesMessages - $incorrectLocationWarnings;
    }

    public function numberOfErrors(): int
    {
        return Statistics::query()
            ->where('request_status', StatsTrackerInterface::REQUEST_STATUS_ERROR)
            ->where('sent_messages', '>', 0)
            ->count();
    }

    public function getRequestsDetails(): array
    {
        return [];
    }
}
