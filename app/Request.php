<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function requester() {
        return $this->belongsTo(User::class);
    }
}
