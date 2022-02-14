<?php

namespace App;

use App\Transformers\ProductVariantTransformer;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = ['id'];
    protected $fillable = ['price', 'status_id', 'name', 'product_id'];
    public $timestamps = false;


    protected $casts = [
        'status_id' => 'integer',
        'product_id' => 'integer'
    ];

    public $transformer = ProductVariantTransformer::class;

    function setData($attributes)
    {
        $data['price'] = $attributes['price'];
        $data['product_id'] = $attributes['product_id'];
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['name'] = $attributes['name'];
        return $data;
    }

    function setDataVariant($product, $variant)
    {
        $data['price'] = $variant->price;
        $data['product_id'] = $product->id;
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['name'] = $variant->name;
        return $data;
    }

    function setUpdate($attributes)
    {
        $this->price = $attributes['price'];
        $this->name = $attributes['name'];
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

    function scopeEnabled($query)
    {
        return $query->where('status_id', Status::byStatus(Status::ENABLED)->value('id'));
    }
}
