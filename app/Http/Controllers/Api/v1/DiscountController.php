<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $_discounts = Discount::all();
        return response()->json($_discounts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $_discount = Discount::create($request->all());
        return response()->json($_discount, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_discount = Discount::find($id);
        return response()->json($_discount);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $_discount = Discount::find($id);
        $_discount->update($request->all());
        return response()->json($_discount, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Discount::destroy($id);
        return response()->json(null, 204);
    }
}
