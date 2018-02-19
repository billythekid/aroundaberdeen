<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{

    protected $fillable = ['user_id', 'site_id', 'lat', 'lng', 'zoom'];
    protected $visible = ['id', 'lat', 'lng', 'zoom'];
    protected $casts = [
      'lat' => 'double',
      'lng' => 'double'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class , 'site_id', 'id');
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }
}
