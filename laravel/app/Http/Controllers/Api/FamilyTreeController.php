<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Spouse;
use App\Models\ParentChild;
use Auth;

class FamilyTreeController extends Controller
{       
    public function index(Request $request)
    {
        $personCount = Person::where('added_by', auth()->user()->id)->count();

        $persons = Person::where('added_by', auth()->user()->id)
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', 'asc')
            ->orderBy('root_user', 'desc')
            ->paginate(10)
            ->withQueryString(); // keep search during pagination

        return view('dashboard', compact('personCount', 'persons'));
    }

    public function index2(){
        $personCount = Person::where('added_by',auth()->user()->id)->count();
        
        $persons = Person::where('added_by',auth()->user()->id)
                    ->orderBy('id','asc')
                    ->orderBy('root_user','desc')
                    ->paginate(10);

        return view('dashboard',compact('personCount','persons'));
    }

    public function familyTree(Request $request){
        if($request->has('root_id')){
            $person = Person::where('added_by',auth()->user()->id)->where('id',$request->root_id)->first();
        }else{
            $person = Person::where('added_by',auth()->user()->id)->where('root_user',1)->first();
        }
        
        if(!$person){
            return redirect('dashboard');
        }

        
        return view('familyTree',compact('person'));
    }


    public function addRoot(Request $request){
        $data = $request->only('name', 'dob', 'gender');
        $data['root_user'] = 1;
        $data['added_by'] = auth()->user()->id;
        return Person::create($data);
    }

    public function add(Request $request){
        $validator = Validator::make($request->all(), [
            'root_id'  => 'required|exists:persons,id',
            'relation' => 'required|in:spouse,child,sibling,parent',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if($request->relation === 'spouse'){
            $childIds = ParentChild::where('parent_id',$request->root_id)->pluck('child_id')->toArray();

            $person = Person::create($request->only('name', 'dob', 'gender'));
            
            // save both sides
            Spouse::create([
                'person_id' => $request->root_id,
                'spouse_id' => $person->id,
            ]);

            foreach ($childIds as $id) {
                ParentChild::create([
                    'parent_id' => $person->id,
                    'child_id'  => $id,
                ]);   
            }


        }else if($request->relation === 'child'){
            $person = Person::create($request->only('name', 'dob', 'gender'));

            ParentChild::create([
                'parent_id' => $request->root_id,
                'child_id'  => $person->id
            ]);

            // If spouse exists, also link
            $spouses = Spouse::where('person_id', $request->root_id)
                ->orWhere('spouse_id', $request->root_id)
                ->get();

            foreach ($spouses as $spouse) {
                $otherParentId = $spouse->person_id == $request->root_id
                    ? $spouse->spouse_id
                    : $spouse->person_id;

                ParentChild::create([
                    'parent_id' => $otherParentId,
                    'child_id'  => $person->id
                ]);
            }
        }else if($request->relation === 'parent'){
            $parents = ParentChild::where('child_id',$request->root_id)->pluck('parent_id')->toArray();
            if(count($parents) >= 2){
                return [
                    'status' => '0',
                    'message' => 'Parent already present',
                ];
            }

            if(count($parents) === 0){
                $person = Person::create($request->only('name', 'dob', 'gender'));

                ParentChild::create([
                    'parent_id' => $person->id,
                    'child_id'  => $request->root_id,
                ]);

                return [
                    'status' => 'success',
                    'message' => 'added success',
                ];
            }else{
                $alreadyAdded = $person = Person::whereIn('id',$parents)->first();
                if($person->gender == $request->gender){
                    $relation = $request->gender === 'female' ? 'Mother' : 'Father';
                    return [
                        'status' => 'success',
                        'message' => $relation.' already added for this user',
                    ];
                }
                
                $person = Person::create($request->only('name', 'dob', 'gender'));

                ParentChild::create([
                    'parent_id' => $person->id,
                    'child_id'  => $request->root_id,
                ]);

                Spouse::create([
                    'person_id' => $alreadyAdded->id,
                    'spouse_id' => $person->id,
                ]);

                return [
                    'status' => 'success',
                    'message' => 'added success',
                ];
            }
        }else if($request->relation === 'sibling'){
            $parents = ParentChild::where('child_id',$request->root_id)->pluck('parent_id')->toArray();
            if(count($parents) === 0){
                return [
                    'status' => '0',
                    'message' => 'Please add parent first',
                ];
            }
            

            $parents = ParentChild::where('child_id',$request->root_id)->pluck('parent_id')->toArray();
            $person = Person::create($request->only('name', 'dob', 'gender'));

            foreach ($parents as $parent) {
                ParentChild::create([
                    'parent_id' => $parent,
                    'child_id'  => $person->id,
                ]);
            }
        }
        
        return [
            'status' => 'success',
            'message' => 'added success',
            'person' => $person ?? [],
        ];
    }

    public function getTree(Request $request){
        $validator = Validator::make($request->all(), [
            'root_id'  => 'required|exists:persons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $person = Person::with([
                        'spouse.spouse',
                        'spouseOf.person',
                        'children' => function ($q) {
                            $q->join('persons as c', 'c.id', '=', 'parent_child.child_id')
                            ->orderBy('c.dob', 'asc')   // 👈 ordering happens here
                            ->select('parent_child.*')
                            ->with('child');
                        },
                        'parents.parent',
                    ])->where('id',$request->root_id)
                    ->first();
        
        $person->siblings = $person->siblings();
        
        return [
            'status' => 1,
            'message' => 'Success',
            'data' => $person,
        ];

    }

    public function viewUser(Request $request){
        $validator = Validator::make($request->all(), [
            'root_id'  => 'required|exists:persons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $person = Person::with([
                        'spouse.spouse',
                        'spouseOf.person',
                    ])->where('id',$request->root_id)
                    ->first();

        return [
            'status' => 1,
            'message' => 'Success',
            'data' => $person,
        ];   
    }
}
