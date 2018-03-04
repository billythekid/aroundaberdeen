<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Point extends Model
  {

    protected $visible = ['id', 'lat', 'lng', 'name'];
    protected $casts = ['lat' => 'float', 'lng' => 'float'];

    public function map() {
      return $this->belongsTo(Map::class);
    }

    public function tags() {
      return $this->belongsToMany(Tag::class);
    }

  }
