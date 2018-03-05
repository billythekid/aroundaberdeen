<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Map extends Model
  {
    protected $visible = ['id', 'lat', 'lng', 'zoom', 'route'];
    protected $casts = [
      'lat'   => 'double',
      'lng'   => 'double',
      'route' => 'json',
    ];

    public function user() {
      return $this->belongsTo(User::class);
    }

    public function site() {
      return $this->belongsTo(Site::class, 'site_id', 'id');
    }

    public function points() {
      return $this->hasMany(Point::class)->orderBy('order');
    }
  }
