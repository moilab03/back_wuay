<?php

namespace App;

use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'commerce_id',
        'category_id',
        'status_id',
        'name',
        'description',
        'code',
        'price_sale',
        'price_discount',
        'preparation_time',
        'product_bank_id'
    ];

    protected $casts = [
        'commerce_id' => 'integer',
        'category_id' => 'integer',
        'status_id' => 'integer',
        'price_sale' => 'integer',
        'price_discount' => 'integer',
    ];

    public $transformer = ProductTransformer::class;

    function setData($attributes, $commerce)
    {
        $data['commerce_id'] = $commerce->id;
        $data['category_id'] = $attributes['category_id'];
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['name'] = $attributes['name'];
        $data['description'] = $attributes['description'];
        $data['code'] = $attributes['code'];
        $data['price_sale'] = $attributes['price_sale'];
        $data['price_discount'] = $attributes['price_discount'] > 0 ? $attributes['price_discount'] : 0;
        $data['preparation_time'] = $attributes['preparation_time'];
        return $data;
    }


    function setDataBank($attributes, $commerce , $product)
    {
        $data['commerce_id'] = $commerce->id;
        $data['category_id'] = $attributes['category_id'];
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['name'] = $attributes['name'];
        $data['description'] = $attributes['description'];
        $data['code'] = $attributes['code'];
        $data['price_sale'] = $attributes['price_sale'];
        $data['price_discount'] = $attributes['price_discount'] > 0 ? $attributes['price_discount'] : 0;;
        $data['preparation_time'] = $attributes['preparation_time'];
        $data['product_bank_id'] = $product->id;
        return $data;
    }

    function updateData($attributes)
    {
        $this->category_id = $attributes['category_id'];
        $this->name = $attributes['name'];
        $this->description = $attributes['description'];
        $this->code = $attributes['code'];
        $this->price_sale = $attributes['price_sale'];
        $this->price_discount = $attributes['price_discount'];
        $this->preparation_time = $attributes['preparation_time'];
        return $this;
    }

    function changeStatus()
    {
        $status = Status::find($this->status_id);
        $this->status_id = Status::byStatus($status->status === Status::ENABLED ? Status::DISABLED : Status::ENABLED)->value('id');
        return $this;
    }

    function validateIfHasBank()
    {
        if (Commerce::find($this->commerce_id)->id !== 8) {
            throw new \Exception('No pertenece al banco de productos');
        }
    }

    function category()
    {
        return $this->belongsTo(Category::class);
    }

    function status()
    {
        return $this->belongsTo(Status::class);
    }

    function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    function resource()
    {
        return $this->morphOne(Resource::class, 'obtainable');
    }

    function scopeByStatus($query, $status)
    {
        return $query->where('status_id', $status);
    }
}
