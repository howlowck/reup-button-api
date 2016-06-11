<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public $timestamps = false;

    public function users() {
        return $this->belongsToMany(User::class);
    }
    
    public function requests() {
        return $this->hasMany(Request::class);
    }
}
