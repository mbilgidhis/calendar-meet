<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Model;

class Event extends Model
{
    use SoftDeletes;

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function attendees() {
        return $this->hasMany('App\Attendee');
    }
}
