<?php

namespace App\Http\Controllers;

use App\Models\SystemLock;
use Illuminate\Http\Request;

class LockController extends Controller
{
    public function lockSystem(Request $request)
    {
        $today = now()->toDateString();
        $lock = SystemLock::updateorCreate(
            ['date' => $today],
            ['is_locked' => true]
        );
        return response()->json([
            'message' => 'System is locked for today: ' . $today,
            'success' => true,
            'is_locked' => true,
        ]);
    }

    public function checkLock()
    {
        $today = now()->toDateString();
        $lock = SystemLock::where('date', $today)->first();

        if ($lock && $lock->is_locked) {
            return response()->json([
                'message' => 'System is locked for today: ' . $today,
                'success' => true,
                'is_locked' => true,
            ]);
        }

        return response()->json([
            'message' => 'System is not locked for today: ' . $today,
            'success' => true,
            'is_locked' => false,
        ]);
    }
}
