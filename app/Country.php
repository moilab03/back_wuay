<?php

namespace App;

use App\Transformers\CountryTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed country
 */
class Country extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['country'];
    public $timestamps = false;
    public $transformer = CountryTransformer::class;

    const COLOMBIA = 'Colombia';

    const COUNTRIES = [
        ['country' => self::COLOMBIA]
    ];

    function departments()
    {
        return $this->hasMany(Department::class);
    }
}
