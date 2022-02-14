<?php

namespace App;

use App\Transformers\CityTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed city
 */
class City extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['city', 'department_id'];
    protected $casts = ['department_id' => 'integer'];
    public $timestamps = false;

    public $transformer = CityTransformer::class;

    const BOGOTA = 'Bogotá D.C';

    const CITIES = [[
        'department_id' => 1,
        'city' => self::BOGOTA
    ]];
}
