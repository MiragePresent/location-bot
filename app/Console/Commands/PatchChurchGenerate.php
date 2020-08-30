<?php

namespace App\Console\Commands;

use App\Models\Church;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
     *
     * @param Filesystem $fileSystem
     */
    public function handle(Filesystem $fileSystem)
    {
        $count = Church::count();

        $timeMark = Carbon::now()->format('Ymdhi');
        $fileName = sprintf('%s_churches_patch.csv', $timeMark);
        $csvFile = storage_path('geo_decode/' . $fileName);

        $this->info('Checking addresses for ' . $count . ' churches');
        $progress = $this->output->createProgressBar($count);

        $fileSystem->put($csvFile, 'name;object_id;address;latitude;longitude');

        Church::chunk(100, function ($churches) use ($fileSystem, $csvFile, $progress) {
            foreach ($churches as $church) {
                $mapData = Geocode::make()->address($church->address);

                if ($mapData
                    && (round($church->latitude, 6) !== round($mapData->latitude(), 6)
                    || round($church->longitude, 6) !== round($mapData->longitude(), 6))
                ) {
                    $line = sprintf(
                        "\n\"%s\";%s;\"%s\";%s;%s",
                        str_replace('"', '""', $church->name),
                        $church->object_id,
                        str_replace('"', '""', $church->address),
                        $mapData->latitude(),
                        $mapData->longitude()
                    );
                    $fileSystem->append($csvFile, $line);
                }

                $progress->advance();
            }
        });

        $progress->finish();
        $this->line('');
        $this->line('Check the file ' . $csvFile);
    }
}
