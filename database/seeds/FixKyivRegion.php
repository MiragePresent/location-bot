<?php

use App\Models\Region;
use Illuminate\Database\Seeder;

/**
 * Class FixKievRegion
 *
 * @author Davyd Holovii <mirage.present@gmail.com>
 * @since  06.08.2019
 */
class FixKyivRegion extends Seeder
{
    public function run()
    {
        /** @var Region $kyivsky */
        $kyivsky = Region::where('name', 'Київська')->first();

        if (empty($kyivsky)) {
            throw new Exception("Issue cannot be resolved. 'Київська' not found");
        }

        /** @var Region $kyiv */
        $kyiv = Region::where('name', 'Київ')->first();

        if (empty($kyiv)) {
            $this->command->warn("Region is already fixed");
        }

        $kyiv->cities()->update(['region_id' => $kyivsky->id]);
        $kyiv->delete();

        \Illuminate\Support\Facades\Artisan::call("cache:clear");

        $this->command->info('Issue is resolved');
    }
}
