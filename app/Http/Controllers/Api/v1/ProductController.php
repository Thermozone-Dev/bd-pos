<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $_products = Product::all();
        return response()->json($_products);
    }

    public function store(Request $request)
    {
        $_product = Product::create($request->all());
        return response()->json($_product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_product = Product::find($id);
        return response()->json($_product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $_product = Product::findFirst($id);
        $_product->update($request->all());
        return response()->json($_product, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::destroy($id);
        return response()->json(null, 204);
    }
}
