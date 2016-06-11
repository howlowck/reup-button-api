<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
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

//    protected $appends = [
//        'requests'
//    ];

    public function organizations () {
        return $this->belongsToMany(Organization::class)->withPivot('role');
    }

    public function requests() {
        return $this->hasMany(Request::class);
    }

    public function subscriptions() {
        return $this->hasMany(Subscription::class);
    }

    public function subscribeTags() {
        return $this->hasMany(SubscribeTag::class);
    }

    public function scopeUnfulfilled($query) {
//        return $query->
    }
}
