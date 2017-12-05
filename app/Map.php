<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{

    protected $fillable = ['user_id', 'site_id', 'name'];

    public function points()
    {
        return $this->belongsToMany(Point::class);
    }
}
