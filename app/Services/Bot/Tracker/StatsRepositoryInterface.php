<?php

namespace App\Services\Bot\Tracker;

/**
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  11.04.2022
 */
interface StatsRepositoryInterface
{
    public function numberOfUsers(): int;
    public function numberOfAllRequests(): int;
    public function numberOfAddressesFound(): int;
    public function numberOfErrors(): int;
    public function getRequestsDetails(): array;
    public function numberOfHumanitarianHelpRequests(): int;
}
