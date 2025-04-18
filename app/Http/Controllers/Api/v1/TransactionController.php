<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $_transactions = Transaction::all();
        return response()->json($_transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items.item_id' => 'numeric',
            'items.item_discount_id' => 'numeric',
            'items.item_quantity' => 'numeric',
            'transaction_discount_id' => 'required|numeric',
            'payment_method' => 'required|string',
            'secret_key' => 'required|string',
        ]);
        //Create Transaction
        $_transaction = Transaction::create($request->all());

        //Create Basket Item

        //Update Transaction

        return response()->json($_transaction, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_transaction = Transaction::find($id);
        return response()->json($_transaction);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $_transaction = Transaction::findFirst($id);
        $_transaction->update($request->all());
        return response()->json($_transaction, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Transaction::destroy($id);
        return response()->json(null, 204);
    }
}
