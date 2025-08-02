<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $_packages = Package::with('packageHasPackageInclusive')->get();
        return response()->json($_packages);
    }

    public function store(Request $request)
    {
        $_package = Package::create($request->all());
        return response()->json($_package, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_package = Package::findFirst($id);
        return response()->json($_package);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $_package = Package::findFirst($id);
        $_package->update($request->all());
        return response()->json($_package, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Package::destroy($id);
        return response()->json(null, 204);
    }
}
