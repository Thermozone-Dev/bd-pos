<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ZReadingController extends Controller
{
    public function show()
    {
        $transactions = Transaction::all();

        return response()->json([
            'transactions' => $transactions,
        ], 200);
    }
}
