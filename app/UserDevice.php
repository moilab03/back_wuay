<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'os',
        'token',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public $timestamps = false;

    const IOS = 'ios';
    const WEB = 'web';
    const ANDROID = 'android';

    const OS = [
        self::IOS,
        self::WEB,
        self::ANDROID,
    ];

    public function setData($attributes)
    {
        $data['os'] = $attributes['os'];
        $data['token'] = $attributes['token'];
        $data['user_id'] = auth()->user()->id;

        return $data;
    }

    public function setDataUpdate($attributes)
    {
        $this->os = $attributes['os'];
        $this->token = $attributes['token'];

        return $this;
    }

    public function setDataRegister($attributes, $id)
    {
        $data['os'] = $attributes['os'];
        $data['token'] = $attributes['firebase'];
        $data['user_id'] = $id;

        return $data;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOs($query, $os)
    {
        return $query->where('os', $os);
    }
}
