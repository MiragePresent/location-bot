<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ChurchPatch
 *
 * @property-read int $id
 * @property int $church_id Related church ID
 * @property string $address Updated address
 * @property float $latitude Location latitude
 * @property float $longitude Location longitude
 * @property bool $is_actual  Determines whether the model is actual or it's an old patch
 *
 * @property-read Church $church Related church model
 */
class ChurchPatch extends Model
{
    /**
     * The model table name
     *
     * @var string
     */
    protected $table = 'church_patches';

    /**
     * Model fields
     *
     * @var array
     */
    protected $fillable = [
        'church_id',
        'address',
        'latitude',
        'longitude',
        'original',
    ];

    /**
     * Auto convert data to needed format
     *
     * @var array
     */
    protected $casts = [
        'original' => 'array',
    ];

    public function church()
    {
        return $this->belongsTo(Church::class);
    }
}
