<?php

namespace App;

use App\Transformers\CommerceTransformer;
use Illuminate\Database\Eloquent\Model;
use Malhal\Geographical\Geographical;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed nit
 * @property mixed contact
 * @property mixed email
 * @property mixed address
 * @property mixed phone
 * @property mixed web
 * @property mixed latitude
 * @property mixed longitude
 * @property mixed attention_schedule
 * @property mixed quantity_table
 * @property mixed city_id
 * @property mixed status_id
 */
class Commerce extends Model
{
    use Geographical;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'nit',
        'contact',
        'email',
        'address',
        'phone',
        'web',
        'latitude',
        'longitude',
        'attention_schedule',
        'quantity_table',
        'city_id',
        'status_id',
        'user_id',
        'security_code'
    ];

    protected $casts = [
        'city_id' => 'integer',
        'status_id' => 'integer',
        'user_id' => 'integer'
    ];

    public $transformer = CommerceTransformer::class;
    protected static $kilometers = true;

    function setDataCommerce($attributes)
    {
        $data['name'] = $attributes['name'];
        $data['nit'] = $attributes['nit'];
        $data['contact'] = $attributes['contact'];
        $data['email'] = $attributes['email'];
        $data['address'] = $attributes['address'];
        $data['phone'] = $attributes['phone'];
        $data['web'] = $attributes['web'];
        $data['latitude'] = $attributes['latitude'];
        $data['longitude'] = $attributes['longitude'];
        $data['attention_schedule'] = $attributes['attention_schedule'];
        $data['quantity_table'] = $attributes['quantity_table'];
        $data['city_id'] = $attributes['city_id'];
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['user_id'] = auth()->user()->id;
        $data['security_code'] = $this->generateCode();
        return $data;
    }


    function setDataUpdateCommerce($attributes)
    {
        $this->name = $attributes['name'];
        $this->nit = $attributes['nit'];
        $this->contact = $attributes['contact'];
        $this->email = $attributes['email'];
        $this->address = $attributes['address'];
        $this->phone = $attributes['phone'];
        $this->web = $attributes['web'];
        $this->latitude = $attributes['latitude'];
        $this->longitude = $attributes['longitude'];
        $this->attention_schedule = $attributes['attention_schedule'];
        $this->quantity_table = $attributes['quantity_table'];
        $this->city_id = $attributes['city_id'];
        return $this;
    }

    function generateCode()
    {
        $number = \Illuminate\Support\Str::random(6);
        if (self::where('security_code', $number)->count() > 0) self::generateNumber();
        return $number;
    }


    function validateUserAdministrator()
    {
        if ($this->user_id !== auth()->user()->id) {
            throw new \Exception('No eres el administrador de este comercio');
        }
    }

    function city()
    {
        return $this->belongsTo(City::class);
    }


    function status()
    {
        return $this->belongsTo(Status::class);
    }

    function resource()
    {
        return $this->morphOne(Resource::class, 'obtainable');
    }

    function products()
    {
        return $this->hasMany(Product::class);
    }
}
