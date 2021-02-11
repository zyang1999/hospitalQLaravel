<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Models\User;

class SpecialtyController extends Controller
{
    public function getSpecialties(Request $request){
        
        if($request->user()->specialty){
            $specialties = $request->user()->specialty->get();
        }else {
            $specialties = Specialty::all()->unique('specialty')->values()->all();
            if($request->doctorId != 'All'){
                $specialties = User::find($request->doctorId)->specialty()->get();
            }
        }

        return response()->json([
            'specialties' => $specialties
        ]);
    }

    public function getSpecialtiesView (){
        $specialties = Specialty::where('specialty', 'not like', 'Phamarcist')->pluck('specialty')->unique();
        
        return view('queue', ['specialties' => $specialties]);
        
    }
}
