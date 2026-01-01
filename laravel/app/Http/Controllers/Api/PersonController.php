<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Spouse;
use App\Models\ParentChild;

class PersonController extends Controller
{
    public function availableChildren($id)
    {   
        $childID = ParentChild::where('parent_id',$id)->pluck('child_id');
        
        return Person::query()
            ->whereIn('id',$childID)
            ->get();
    }

    public function show($id)
    {
        return Person::with([
            'spouse.spouse',
            'spouseOf.person',
            'children.child'
        ])->findOrFail($id);
    }

    public function addChild(Request $request, $parentId)
    {   
         $parent = Person::findOrFail($parentId);

        ParentChild::create([
            'parent_id' => $parentId,
            'child_id'  => $request->child_id
        ]);

        // If spouse exists, also link
        $spouse = Spouse::where('person_id', $parentId)
            ->orWhere('spouse_id', $parentId)
            ->first();

        if ($spouse) {
            $otherParentId = $spouse->person_id == $parentId
                ? $spouse->spouse_id
                : $spouse->person_id;

            ParentChild::create([
                'parent_id' => $otherParentId,
                'child_id'  => $request->child_id
            ]);
        }

        return response()->json(['message' => 'Child added to both parents']);

    }


    public function addSpouse(Request $request, $id)
    {
        $person = Person::findOrFail($id);
        $spouseId = $request->spouse_id;

        // prevent duplicate
        Spouse::where('person_id', $id)->orWhere('spouse_id', $id)->delete();

        // save both sides
        Spouse::create([
            'person_id' => $id,
            'spouse_id' => $spouseId
        ]);

        return response()->json(['message' => 'Spouse added']);
    }

    public function store(Request $request)
    {
        return Person::create($request->only('name', 'dob', 'gender'));
    }

    public function list(Request $request)
    {
        return Person::get();
    }

}
