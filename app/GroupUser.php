<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Transformers\GroupUserTransformer;

/**
 * @property mixed id
 * @property mixed name
 */
class GroupUser extends Model
{
    protected $guarded = ['id'];

    protected $fillable = ['name', 'user_id', 'status_id', 'commerce_id'];

    protected $casts = [
        'user_id' => 'integer',
        'status_id' => 'integer',
        'commerce_id' => 'integer'
    ];

    public $transformer = GroupUserTransformer::class;

    function setData($attributes)
    {
        $data['name'] = $attributes['name'];
        $data['user_id'] = auth()->user()->id;
        $data['status_id'] = Status::byStatus(Status::ENABLED)->value('id');
        $data['commerce_id'] = auth()->user()->current_commerce_id;
        return $data;
    }

    public function savePhotosResource($photos)
    {
        if (empty($photos)) return;

        try {
            $resource = new Resource();
            $photosSaved = [];
            foreach ($photos as $photo) {
                $photosSaved[] = $resource->create(
                    $resource->saveResource(
                        $photo,
                        self::class,
                        $this->id,
                        TypeResource::byType(TypeResource::PHOTO_GROUP_USER)->value('id'),
                        'group_user',
                        true
                    )
                );
            }
            return $photosSaved;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function resources()
    {
        return $this->morphMany(Resource::class, 'obtainable');
    }


    public function groupInterests()
    {
        return $this->hasMany(GroupInterest::class);
    }

    function scopeByUser($query, $user)
    {
        return $query->where('user_id', $user);
    }

    function scopeByCommerce($query, $commerce)
    {
        return $query->where('commerce_id', $commerce);
    }


    function scopeByStatus($query, $status)
    {
        return $query->where('status_id', Status::byStatus($status)->value('id'));
    }
}
