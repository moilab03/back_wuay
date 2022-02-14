<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed department
 * @property mixed id
 */
class Department extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['department', 'country_id'];
    public $timestamps = false;
    protected $casts = ['country_id' => 'integer'];

    const CUNDINAMARCA = 'Cundinamarca';

    const DEPARTMENTS = [[
        'department' => self::CUNDINAMARCA,
        'country_id' => 1
    ]];

    function cities()
    {
        return $this->hasMany(City::class);
    }
}
