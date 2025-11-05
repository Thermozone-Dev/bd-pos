<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Terminal;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    public function index(){
        $terminals = Terminal::all();

        return response()->json($terminals, 200);
    }

    public function getTerminalID(Request $request){
        $device_name = $request->header('Device-Name');

        $terminal = Terminal::where('name', $device_name)->first();

        if (!$terminal) {
            $terminal = Terminal::create(['name' => $device_name]);
        }

        return response()->json(['terminal_id' => $terminal->id], 200);
    }
}
