<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Models\User;

class SpecialtyController extends Controller
{
    public function getSpecialties(Request $request){
        $specialties = Specialty::all();

        if($request->doctorId != 'All'){
            $specialties = User::find($request->doctorId)->specialty()->get();
        }

        return response()->json([
            'specialties' => $specialties
        ]);
    }
}
