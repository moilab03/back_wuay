<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Resource extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'url',
        'obtainable_type',
        'obtainable_id',
        'type_resource_id'
    ];

    public $timestamps = false;


    public function saveResource($url, $type, $id, $typeResource, $path, $isImage = true)
    {
        try {
            $data['url'] = $isImage ? $this->resizeResource($url, $path) : $this->uploadFile($url, $path);
            $data['obtainable_type'] = $type;
            $data['obtainable_id'] = $id;
            $data['type_resource_id'] = $typeResource;
            return $data;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    protected function resizeResource($url, $path)
    {
        try {
            $fitImage = Image::make($url);
            $extension = '.' . explode("/", $fitImage->mime())[1];
            $fileName = md5(random_int(1, 10000000) . microtime());
            $storage = Storage::disk('public');
            $storage->put("image/$path/$fileName$extension", $fitImage->encode());
            return "/image/$path/$fileName$extension";
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    protected function uploadFile($file, $path)
    {
        try {
            $storage = Storage::disk('public');
            $name = time();
            $route = "/$path/$name.png";
            $storage->put($route, $file);
            return $route;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function obtainable()
    {
        return $this->morphTo();
    }

    public function typeResource()
    {
        return $this->belongsTo(TypeResource::class);
    }

    public function scopeByTypeResource($query, $type)
    {
        return $query->where('type_resource_id', TypeResource::byType($type)->value('id'));
    }

}
