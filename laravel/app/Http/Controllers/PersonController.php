<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Spouse;
use App\Models\ParentChild;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonController extends Controller
{   
    public function treeView($id)
    {
        $root = Person::with([
                    'childrenPersons',
                    'spouses',
                    'spousesOf'
                ])->findOrFail($id);
        $tree = $this->buildTree($root);
        
        return view('tree.view', compact('tree', 'root'));
    }

    public function edit($id)
    {
        $person = Person::findOrFail($id);
        return view('persons.edit', compact('person'));
    }

    public function update(Request $request, $id)
    {   
        $request->validate([
            'name'   => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'dob'    => 'required|min:4|max:4',
            'address' => 'nullable|max:100',
        ]);

        $request->merge([
            'dob' => $request->dob.'-01-01',
        ]);
        
        $person = Person::findOrFail($id);
        $person->update($request->only(['name', 'gender', 'dob','address']));

        return redirect('/dashboard')
            ->with('success', 'Person updated successfully.');
    }

    public function destroy($id)
    {   
        $childCount = ParentChild::where('parent_id',$id)->count();
        
        if($childCount){
            return back()->with('error', 'This person has children. Please delete the children first, then delete the parent.');
        }

        Person::findOrFail($id)->delete();

        return back()->with('success', 'Person deleted successfully.');
    }

    public function treePdf($id)
    {
        $root = Person::with(['childrenPersons', 'spouses'])->findOrFail($id);

        $tree = $this->buildTree($root);

        $pdf = Pdf::loadView('pdf.family-tree', compact('tree', 'root'))
                ->setPaper('A4', 'portrait');

        return $pdf->download('family_tree_'.$root->name.'.pdf');
    }

    private function buildTree(Person $person)
    {   
        return [
            'person'   => $person,
            'spouses'  => $person->allSpouses(),
            'children' => $person->childrenPersons->map(function ($child) {
                return $this->buildTree($child);
            })
        ];
    
    }

}
