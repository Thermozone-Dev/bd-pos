<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $_payment_methods = PaymentMethod::where('is_enabled', true)
            ->get();

        $_data = [];
        foreach ($_payment_methods as $key => $payment_method) {
            $_data[$key] = [
                'id' => $payment_method->id,
                'name' => $payment_method->name,
                'is_digital' => $payment_method->is_digital,
            ];
        }

        return response()->json($_data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $_payment_method = PaymentMethod::create($request);
        return response()->json($_payment_method, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $_payment_method = PaymentMethod::find($id);
        return response()->json($_payment_method);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $_payment_method = PaymentMethod::find($id);
        $_payment_method->update($request);
        return response()->json($_payment_method, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        PaymentMethod::destroy($id);
        return response()->json(null, 204);
    }
}
