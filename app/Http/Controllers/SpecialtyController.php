<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialty;

class SpecialtyController extends Controller
{
    public function getSpecialties(Request $request){
        return response()->json([
            'specialties' => Specialty::all()  
        ]);
    }
}
