<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['user_id', 'tagtype_id', 'map_id', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tagType()
    {
        return $this->belongsTo(Tagtype::class);
    }

    public function map()
    {
        return $this->belongsTo(Map::class);
    }

    public function points()
    {
        return $this->belongsToMany(Point::class);
    }

}
