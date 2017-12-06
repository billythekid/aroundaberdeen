<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function maps()
    {
        return $this->hasMany(Map::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class);
    }

    public function tagtypes()
    {
        return $this->hasMany(Tagtype::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function getFirstNameAttribute()
    {
        return explode(' ', $this->name)[0];
    }
}
