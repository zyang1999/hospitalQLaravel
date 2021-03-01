<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SpecialtyController extends Controller
{
    public function getSpecialties(Request $request)
    {
        $specialties = Specialty::whereHas("user", function ($query) {
            $query->where("status", "VERIFIED");
        })
            ->where("specialty", "not like", "Pharmacist")
            ->get()
            ->unique("specialty")
            ->values()
            ->all();

        if ($request->doctorId != "All") {
            $specialties = User::find($request->doctorId)
                ->specialty()
                ->get();
        }

        return response()->json([
            "specialties" => $specialties,
        ]);
    }

    public function getSpecialtiesView()
    {
        $specialties = Specialty::where("specialty", "not like", "Pharmacist")
            ->whereHas("user", function (Builder $query) {
                $query->where("status", "VERIFIED");
            })
            ->pluck("specialty")
            ->unique();

        return view("queue", ["specialties" => $specialties]);
    }

    public function getQrCodeVIew()
    {
        $specialties = Specialty::where("specialty", "not like", "Pharmacist")
            ->whereHas("user", function (Builder $query) {
                $query->where("status", "VERIFIED");
            })
            ->pluck("specialty")
            ->unique();

        return view("qr_code", [
            "specialties" => $specialties,
            "url" => config("app.url"),
        ]);
    }
}
