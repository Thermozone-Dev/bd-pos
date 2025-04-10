<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $_items = Item::all();
        return response()->json($_items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $_item = Item::create($request->all());
        return response()->json($_item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_item = Item::findFirst($id);
        return response()->json($_item);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $_item = Item::findFirst($id);
        $_item->update($request->all());
        return response()->json($_item, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Item::destroy($id);
        return response()->json(null, 204);
    }
}
