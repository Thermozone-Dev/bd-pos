<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Package;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function products()
    {
        $_products = DB::table('items')
                        ->join('products', 'items.product_id', '=', 'products.id')
                        ->select(['items.id', 'items.product_id', 'products.name', 'products.price'])
                        ->get();

        foreach ($_products as $product) {
            $_media = Product::find($product->product_id)->getMedia();
            $product->image_url = $_media->first() ? $_media->first()->getUrl() : null;
        }

        return response()->json($_products);
    }

    public function packages()
    {
        $_packages = DB::table('items')
                        ->join('packages', 'items.package_id', '=', 'packages.id')
                        ->select(['items.id', 'items.package_id', 'packages.name', 'packages.price'])
                        ->get();

        foreach ($_packages as $package) {
            $_media = Package::find($package->package_id)->getMedia();
            $package->image_url = $_media->first() ? $_media->first()->getUrl() : null;
        }
        return response()->json($_packages);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_item = Item::find($id);
        return response()->json($_item);
    }

    public function product(string $id)
    {
        $_product = DB::table('items')
                        ->join('products', 'items.product_id', '=', 'products.id')
                        ->select(['items.id', 'items.product_id', 'products.name', 'products.price'])
                        ->where('items.id', '=', $id)
                        ->get();

        $_media = Product::find($_product->first()->product_id)->getMedia();
        $_product->first()->image_url = $_media->first() ? $_media->first()->getUrl() : null;
        return response()->json($_product);
    }

    public function package(string $id)
    {
        $_package = DB::table('items')
                        ->join('packages', 'items.package_id', '=', 'packages.id')
                        ->select(['items.id', 'items.package_id', 'packages.name', 'packages.price'])
                        ->where('items.id', '=', $id)
                        ->get();

        $_media = Package::find($_package->first()->package_id)->getMedia();
        $_package->first()->image_url = $_media->first() ? $_media->first()->getUrl() : null;
        return response()->json($_package);
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
