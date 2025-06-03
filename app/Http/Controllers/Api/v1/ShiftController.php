<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function start(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required',
        ]);

        $user = auth()->user()->id;
        $shift = Shift::create([
            'user_id' => $user,
            'time_in' => now(),
            'opening_balance' => (float) $request->opening_balance,
        ]);

        return response()->json($shift->id, 201);
    }

    public function end(Request $request)
    {
        $request->validate([
            'ending_balance' => 'required',
            'id' => 'required|numeric',
        ]);

        $shift = Shift::find($request->id);

        if ($shift && !$shift->time_out) {
            $shift->update([
                'time_out' => now(),
                'ending_balance' => $request->ending_balance,
            ]);
            return response()->json($shift, 200);
        }

        return response()->json(['message' => 'No active shift found'], 404);
    }

    public function show($id)
    {
        $shift = Shift::where('user_id', auth()->user()->id)
            ->where('created_at', '>=', now()->startOfDay())
            ->where('created_at', '<=', now()->endOfDay())
            ->where('id', $id)
            ->whereNull('time_out')
            ->first();

        if (!$shift) {
            return response()->json(['message' => 'Shift not found or already ended.'], 404);
        }

        return response()->json($shift, 200);
    }
}
