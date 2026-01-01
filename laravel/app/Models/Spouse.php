<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spouse extends Model
{
    protected $table = 'spouses';

    protected $fillable = ['person_id', 'spouse_id'];

    public function person()
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function spouse()
    {
        return $this->belongsTo(Person::class, 'spouse_id');
    }
}
