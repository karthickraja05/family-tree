<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentChild extends Model
{   
    protected $table = 'parent_child';

    protected $fillable = ['parent_id', 'child_id'];

    public function parent()
    {
        return $this->belongsTo(Person::class, 'parent_id');
    }

    public function child()
    {
        return $this->belongsTo(Person::class, 'child_id');
    }
}
