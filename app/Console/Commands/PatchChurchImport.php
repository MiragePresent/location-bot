<?php

namespace App\Console\Commands;

use App\Models\Church;
use App\Models\ChurchPatch;
use Illuminate\Console\Command;
use SpreadsheetReader;

/**
 * Class PatchChurch
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  01.03.2020
 */
class PatchChurchImport extends Command
{
    /**
     * Command name.
     *
     * @var string
     */
    protected $signature = 'patch:church:import {file?}';

    /**
     * Command description
     *
     * @var string
     */
    protected $description = 'Creates church addresses patches from a file';

    /**
     * Does all the command work
     */
    public function handle()
    {
        $file = $this->argument('file') ?? config('bot.patches_file');

        // End work if file does not exist
        if (!file_exists($file)) {
            $this->error("File {$file} does not exist.");

            return;
        }

        $reader = new SpreadsheetReader($file);
        $fields = null;

        foreach ($reader as $row) {
            if (empty($fields)) {
                $fields = $row;
            } else {
                $this->createPatch(array_combine($fields, $row));
            }
        }
    }

    private function createPatch(array $data)
    {
        /** @var Church $church */
        $church = Church::where('object_id', $data['object_id'] ?? null)->first();

        if (empty($church)) {
            $this->warn('Church object ' . $data['object_id'] . ' not found');

            return;
        }

        $diff = [];

        foreach (['address', 'latitude', 'longitude'] as $param) {
            if ($church->{$param} !== $data[$param]) {
                $diff[$param] = $data[$param];
            }
        }

        if (empty($diff)) {
            $this->warn('Church object' . $data['object_id'] . ' is identical to the patch');

            return;
        }

        $church->update($diff);
        $church->patches()->create(array_merge(
            $diff,
            ['original' => [
                'address' => $church->address,
                'latitude' => $church->latitude,
                'longitude' => $church->longitude
            ]]
        ));
    }
}
