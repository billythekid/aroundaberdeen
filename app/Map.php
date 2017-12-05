<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{

    protected $fillable = ['user_id', 'site_id', 'name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function points()
    {
        return $this->belongsToMany(Point::class);
    }
}
