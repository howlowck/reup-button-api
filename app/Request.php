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

    public function scopeOpen($query) {
        return $query->where('fulfilled', false);
    }

    public function scopeOfOrgs($query, array $orgIds) {
        return $query->whereIn('organization_id', $orgIds);
    }
}
