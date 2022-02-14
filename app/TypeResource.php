<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeResource extends Model
{
    protected $fillable = ['type'];
    protected $guarded = ['id'];
    public $timestamps = false;

    const QR_SECURITY = 'QR seguridad';
    const QR_TABLE = 'QR mesa';
    const LOGO_COMMERCE = 'Logo comercio';
    const COVER_COMMERCE = 'Portada comercio';
    const PHOTO_PRODUCT = 'Foto producto';
    const PHOTO_GROUP_USER = 'Foto grupo usuario';

    const TYPES_RESOURCES = [
        ['type' => self::QR_SECURITY],
        ['type' => self::QR_TABLE],
        ['type' => self::LOGO_COMMERCE],
        ['type' => self::COVER_COMMERCE],
        ['type' => self::PHOTO_PRODUCT],
        ['type' => self::PHOTO_GROUP_USER]
    ];

    function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
