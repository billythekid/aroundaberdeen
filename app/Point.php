<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = ['user_id', 'lat', 'long', 'name'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maps()
    {
        return $this->belongsTo(Map::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

}
