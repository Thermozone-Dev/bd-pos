<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class XReadingController extends Controller
{

    public function show()
    {
        $shift = auth()->user()->shifts()->latest()->get();
        dd($shift);
    }

}
