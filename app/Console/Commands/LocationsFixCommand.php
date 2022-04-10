<?php

namespace App\Console\Commands;

use App\Models\Church;
use Illuminate\Support\Facades\DB;

/**
 * Class PrintCsvCommand
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  08.04.2022
 */
class LocationsFixCommand extends \Illuminate\Console\Command
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

        $csv = file_get_contents($filename);
        $rows = explode("\n", $csv);

        if (empty($rows)) {
            $this->error('Cannot fix locations. Patch file is empty.');

            return 1;
        }

        $header = array_shift($rows);
        $header = explode(",", $header);

        if (empty($header)) {
            $this->error('Cannot fix locations. CSV file header is invalid');

            return 1;
        }

        if (!empty(array_diff($header, ['object_id', 'name', 'latitude', 'longitude']))) {
            $this->error('Cannot fix locations. Unknown CSV file format');

            return 1;
        }

        foreach ($rows as $rowNum => $row) {
            $data = explode(",", $row);

            if (empty($row)) {
                continue;
            }

            if (count($data) !== count($header)) {
                $this->warn("Line {$rowNum} is incorrect. Skipping...");

                continue;
            }


            $update = array_combine($header, $data);
            /** @var Church $church */
            $church = Church::query()->where('object_id', $update['object_id'])->first();

            if (!$church) {
                $this->warn(sprintf('Cannot find update %s. Skipping...', $update['object_id']));

                continue;
            }

            if (!(float)$update['latitude'] || !(float)$update['longitude']) {
                $this->warn(sprintf('New coordinates for object %s are incorrect. Skipping...', $update['object_id']));

                continue;
            }

            DB::beginTransaction();

            try {
                $church->latitude = $update['latitude'];
                $church->longitude = $update ['longitude'];
                $church->save();

                $updated++;

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();

                $this->warn(sprintf(
                    'Cannot update object %s due to db error: %s',
                    $update['object_id'],
                    $e->getMessage()
                ));
            }
        }

        $this->getOutput()->success(sprintf("%s church(es) successfully updated", $updated));

        return 0;
    }
}
