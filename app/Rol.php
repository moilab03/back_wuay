<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['rol'];
    public $timestamps = false;

    const ADMINISTRATOR = 'Administrador';
    const USER = 'Usuario';
    const ADMINISTRATOR_COMMERCE = 'Administrador comercio';

    const ROLES = [
        ['rol' => self::ADMINISTRATOR],
        ['rol' => self::USER],
        ['rol' => self::ADMINISTRATOR_COMMERCE]
    ];


    function scopeByRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }
}
