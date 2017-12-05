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
        return $this->belongsToMany(Map::class);
    }

}
