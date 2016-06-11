<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    public function requests() {
        return $this->belongsToMany(Request::class);
    }

    protected $fillable = [
        'name'
    ];
}
