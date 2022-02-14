<?php

namespace App;

use App\Transformers\InterestTransformer;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    protected $fillable = ['name', 'status_id'];

    protected $guarded = ['id'];

    public $timestamps = false;

    public $transformer = InterestTransformer::class;
    const INTERESTS = ['Interes 1', 'Interes 2', 'Interes 3', 'Interes 4', 'Interes 5'];

    function scopeByStatus($query, $status)
    {
        return $query->where('status_id' ,Status::byStatus($status)->value('id'));
    }
}
