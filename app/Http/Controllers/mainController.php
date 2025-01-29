<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\role;
use App\Models\Specialrole;                                 

class mainController extends Controller
{
    public function main()
    {
        $type = role::select('type')
        ->distinct()
        ->get();
        $management = role::select('Rolename')
        ->where('type', 'Management')
        ->get();
        $centerofheads = role::select('Rolename')
        ->where('type', 'center of heads')
        ->get();

        $improve = Specialrole::join('faculty', 'Specialrole.id', '=', 'faculty.id')
            ->select('specialrole.id', 'specialrole.Role', 'faculty.name')
            ->get();
        return view('main', compact('type', 'management', 'centerofheads' ,'improve'));
    }
    public function addRole(Request $request)
    {
        
        Specialrole::create([
            'id' => $request->fid,
            'type' => $request->workType,
            'Role' => $request->selectedOption,
            'Status' => $request->simpleDropdown
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Role added successfully!'
        ]);
    }
}
