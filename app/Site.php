<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Site extends Model
  {
    protected $fillable = [];
    protected $primaryKey = 'subdomain';
    protected $keyType = 'string';

    protected $visible = ['id','name', 'map'];

    public function user() {
      return $this->belongsTo(User::class);
    }

    public function map() {
      return $this->hasOne(Map::class, 'site_id', 'id' );
    }

  }
