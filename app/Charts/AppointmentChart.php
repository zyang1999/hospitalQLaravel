<?php

declare(strict_types=1);

namespace App\Charts;

use Chartisan\PHP\Chartisan;
use ConsoleTVs\Charts\BaseChart;
use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentChart extends BaseChart
{
    /**
     * Handles the HTTP request for the given chart.
     * It must always return an instance of Chartisan
     * and never a string or an array.
     */
    public function handler(Request $request): Chartisan
    {
        $dates = Appointment::pluck("date")
            ->unique()
            ->values()
            ->all();

        $completed = Appointment::where("status", "COMPLETED")
            ->pluck("date")
            ->countBy()
            ->values()
            ->toArray();

        $cancelled = Appointment::where("status", "CANCELLED")
            ->pluck("date")
            ->countBy()
            ->values()
            ->toArray();

        return Chartisan::build()
            ->labels($dates)
            ->dataset("Completed", $completed)
            ->dataset("Cancelled", $cancelled);
    }
}
