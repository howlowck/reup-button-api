<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function requester() {
        return $this->belongsTo(User::class);
    }
}
