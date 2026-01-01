<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Person extends Model
{   
    protected $table = 'persons';

    protected $fillable = ['name', 'dob', 'gender','added_by','root_user','address'];

     protected static function booted()
    {
        static::creating(function ($person) {
            if (Auth::check()) {
                $person->added_by = Auth::id();
            }
        });
    }

    // Spouse (one-to-one, symmetric)
    public function spouse()
    {
        return $this->hasMany(Spouse::class, 'person_id');
    }

    public function spouseOf()
    {
        return $this->hasMany(Spouse::class, 'spouse_id');
    }

    public function spousesOf()
    {
        return $this->hasMany(Spouse::class, 'spouse_id');
    }

    // Children
    public function children()
    {
        return $this->hasMany(ParentChild::class, 'parent_id');
    }

    // Parents (optional but useful)
    public function parents()
    {
        return $this->hasMany(ParentChild::class, 'child_id');
    }

    // Parents (optional but useful)
    public function siblings()
    {
        $parentIDs = $this->parents()->pluck('parent_id')->toArray();
        // dd($parentIDs);

        $siblingsID = ParentChild::whereIn('parent_id',$parentIDs)
                    ->where('child_id','!=',$this->attributes['id'])
                    ->pluck('child_id')->toArray();

        $siblings = Person::whereIn('id',$siblingsID)->orderBy('dob','asc')->get();
        
        return $siblings;

    }

    public function parentPersons()
    {
        return $this->belongsToMany(
            Person::class,
            'parent_child',
            'child_id',
            'parent_id'
        );
    }

    public function spousePersons()
    {
        return $this->belongsToMany(
            Person::class,
            'spouses',
            'person_id',
            'spouse_id'
        );
    }

    public function spouseOfPersons()
    {
        return $this->belongsToMany(
            Person::class,
            'spouses',
            'spouse_id',
            'person_id'
        );
    }

    public function spouses()
    {
        return $this->belongsToMany(
            Person::class,
            'spouses',
            'person_id',
            'spouse_id'
        );
    }

    public function allSpouses()
    {       
        $spouseIds = $this->spouses()
        ->pluck('spouses.spouse_id')
        ->merge(
            $this->spousesOf()->pluck('spouses.person_id')
        )
        ->unique()
        ->values();

        return Person::whereIn('id', $spouseIds)->get();

    }

    public function childrenPersons()
    {
        return $this->belongsToMany(
            Person::class,
            'parent_child',
            'parent_id',
            'child_id'
        );
    }
    

}

