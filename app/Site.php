<?php

  namespace App;

  use Illuminate\Database\Eloquent\Model;

  class Site extends Model
  {
    protected $fillable = [];
    protected $primaryKey = 'subdomain';
    protected $keyType = 'string';

    protected $visible = ['name'];

    public function user() {
      return $this->belongsTo(User::class);
    }

  }
