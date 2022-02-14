<?php

namespace App;

use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed id
 * @property mixed category
 */
class Category extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['category', 'commerce_id', 'status_id'];
    protected $casts = [
        'commerce_id' => 'integer',
        'status_id' => 'integer',
    ];

    public $timestamps = false;

    public $transformer = CategoryTransformer::class;

    function setData($attributes, $commerce)
    {
        $data['category'] = $attributes['category'];
        $data['commerce_id'] = $commerce;
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        return $data;
    }

    function updateData($attributes)
    {
        $this->category = $attributes['category'];
        return $this;
    }

    function changeStatus()
    {
        $status = Status::find($this->status_id);
        $this->status_id = Status::byStatus($status->status === Status::ENABLED ? Status::DISABLED : Status::ENABLED)->value('id');
        return $this;
    }


    function status()
    {
        return $this->belongsTo(Status::class);
    }

    function products()
    {
        return $this->hasMany(Product::class);
    }

    function scopeByCommerce($query, $commerce)
    {
        return $query->where('commerce_id', $commerce);
    }

    function scopeByStatus($query, $status)
    {
        return $query->where('status_id', $status);
    }
}
