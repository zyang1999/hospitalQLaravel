<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Models\User;

class SpecialtyController extends Controller
{
    public function getSpecialties(Request $request){
        
        if($request->user()->specialty){
            $specialties = $request->user()->specialty;
        }else {
            $specialties = Specialty::all()->unique()->values()->all();
            if($request->doctorId != 'All'){
                $specialties = User::find($request->doctorId)->specialty;
            }
        }

        return response()->json([
            'specialties' => $specialties
        ]);
    }
}
