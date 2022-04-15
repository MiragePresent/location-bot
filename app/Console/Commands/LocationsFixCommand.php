<?php

namespace App\Console\Commands;

use App\Models\Church;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

/**
 * Class PrintCsvCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  08.04.2022
 */
class LocationsFixCommand extends Command
{
    public $signature = 'locations:fix';

    public function handle()
    {
        $updated = 0;
        $filename = storage_path('location_fix.csv');

        if (!file_exists($filename) || is_dir($filename))  {
            $this->error('Cannot fix locations. Patch file not found.');

            return 1;
        }

        $csv = Reader::createFromPath($filename);
        $csv->setHeaderOffset(0);

        $header = $csv->getHeader();
        $rows = $csv->getRecords();

        if (empty($header)) {
            $this->error('Cannot fix locations. CSV file header is invalid');

            return 1;
        }

        if (!empty(array_diff($header, ['object_id', 'name', 'latitude', 'longitude', 'address']))) {
            $this->error('Cannot fix locations. Unknown CSV file format');

            return 1;
        }

        foreach ($rows as $row) {
            /** @var Church $church */
            $church = Church::query()->where('object_id', $row['object_id'])->first();

            if (!$church) {
                $this->warn(sprintf('Cannot find update %s. Skipping...', $row['object_id']));

                continue;
            }

            if (!(float) $row['latitude'] || !(float) $row['longitude']) {
                $this->warn(sprintf('New coordinates for object %s are incorrect. Skipping...', $row['object_id']));

                continue;
            }

            DB::beginTransaction();

            try {
                $church->latitude = $row['latitude'];
                $church->longitude = $row ['longitude'];

                if ($row['address']) {
                    $church->address = $row['address'];
                }

                $church->save();

                $updated++;

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                $this->warn(sprintf(
                    'Cannot update object %s due to db error: %s',
                    $row['object_id'],
                    $e->getMessage()
                ));
            }
        }

        $this->getOutput()->success(sprintf("%s church(es) successfully updated", $updated));

        return 0;
    }
}
