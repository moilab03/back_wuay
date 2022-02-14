<?php

namespace App;

use App\Transformers\UserTransformer;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed id
 * @property mixed name
 * @property mixed phone
 * @property mixed email
 * @property string password
 * @property mixed current_commerce_id
 * @property mixed terms_and_conditions
 * @property mixed principal_group_user_id
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'status_id', 'rol_id', 'password', 'current_commerce_id', 'terms_and_conditions', 'principal_group_user_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'status_id' => 'integer',
        'rol_id' => 'integer',
        'current_commerce_id' => 'integer'
    ];

    public $transformer = UserTransformer::class;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    function setDataUser($attributes, $phone)
    {
        $data['phone'] = $phone;
        $data['name'] = $attributes['name'];
        $data['current_commerce_id'] = $attributes['commerce_id'];
        $data['rol_id'] = Rol::byRol(Rol::USER)->value('id');
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['terms_and_conditions'] = true;
        return $data;

    }

    function setDataAdminCommerce($attributes)
    {
        $data['email'] = $attributes['email'];
        $data['name'] = $attributes['name'];
        $data['password'] = Hash::make($attributes['password']);
        $data['rol_id'] = Rol::byRol(Rol::ADMINISTRATOR_COMMERCE)->value('id');
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['terms_and_conditions'] = true;
        return $data;
    }

    function setDataUpdateCommerce($attributes)
    {
        $this->name = $attributes['name'];
        if ($attributes->has('password')) {
            $this->password = Hash::make($attributes['password']);
        }
        $this->email = $attributes['email'];
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

    function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    function commerce()
    {
        return $this->hasOne(Commerce::class);
    }

    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }

    function scopeByRol($query, $rol)
    {
        return $query->where('rol_id', Rol::byRol($rol)->value('id'));
    }

}
