<?php

namespace App\Console\Commands;

use App\Models\Church;
use Illuminate\Console\Command;
use Jcf\Geocode\Geocode;

/**
 * Class PatchChurch
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  01.03.2020
 */
class PatchChurchGenerate extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $signature = 'patch:church:generate';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Check churches with wrong coordinates';

    /**
     * Does all the command work
     */
    public function handle()
    {
        $count = Church::count();

        $this->info('Checking addresses for ' . $count . ' churches');
        $progress = $this->output->createProgressBar($count);
        $csvFile = storage_path(time() . '_churches_patch.csv');

        $fs = fopen($csvFile, "w+");
        fwrite($fs,'name,object_id,address,latitude,longitude');

        Church::chunk(100, function ($churches) use ($fs, $progress) {
            foreach ($churches as $church) {
                $mapData = Geocode::make()->address($church->address);

                if ($mapData
                    && (round($church->latitude, 6) !== round($mapData->latitude(), 6)
                    || round($church->longitude, 6) !== round($mapData->longitude(), 6))
                ) {
                    $line = sprintf(
                        "\n%s,%s,%s,%s,%s",
                        '"' . str_replace('"', '""', $church->name) . '"',
                        $church->object_id,
                        "\"{$church->address}\"",
                        $mapData->latitude(),
                        $mapData->longitude()
                    );
                    fwrite($fs, $line);
                }

                $progress->advance();
            }
        });

        fclose($fs);

        $progress->finish();
        $this->line('');
        $this->line('Check the file ' . $csvFile);
    }
}
