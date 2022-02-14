<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['status'];
    public $timestamps = false;

    const ENABLED = 'Habilitado';
    const DISABLED = 'Deshabilitado';
    const INVITED = 'Invitado';
    const REJECTED = 'Rechazado';
    const ACCEPTED = 'Aceptado';
    const FINALIZED = 'Finalizado';
    const SILENT = 'Silenciado';

    const STATUSES = [
        ['status' => self::ENABLED],
        ['status' => self::DISABLED],
        ['status' => self::INVITED],
        ['status' => self::REJECTED],
        ['status' => self::ACCEPTED],
        ['status' => self::FINALIZED],
        ['status' => self::SILENT]
    ];

    function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
