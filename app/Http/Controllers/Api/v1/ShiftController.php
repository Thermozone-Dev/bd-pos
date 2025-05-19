<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function start()
    {
        $user = auth()->user()->id;
        $shift = Shift::create([
            'user_id' => $user,
            'time_in' => now(),
        ]);

        return response()->json($shift->id, 201);
    }

    public function end(String $id)
    {
        $shift = Shift::find($id);

        if ($shift && !$shift->time_out) {
            $shift->update([
                'time_out' => now(),
            ]);
            return response()->json($shift, 200);
        }

        return response()->json(['message' => 'No active shift found'], 404);
    }


}
