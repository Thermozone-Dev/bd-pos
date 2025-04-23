<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $_user = User::where('email', $request->email)->first();

        if (! $_user || ! Hash::check($request->password, $_user->password)){
            throw ValidationException::withMessages([
                'email' => ['incorrect credentials'],
            ]);
        }

        $token = $_user->createToken($request->device_name, ['general:utils'])->plainTextToken;
        return response()->json(['token' => $token], 200);
    }

    public function logout(Request $request){
        $request->validate([
            'device_name' => 'required',
        ]);

        auth()->user()->tokens()->where('name', $request->device_name)->delete();
        return response()->json(null, 204);
    }

    public function checkTokens(Request $request){
        $_tokens = auth()->user()->tokens()->get();
        return response()->json($_tokens, 200);
    }

    public function index(){
        $_users = User::all();
        return response()->json($_users);
    }

    public function show(Request $request){
        $_user = auth()->user();
        return response()->json($_user);
    }

    public function hasRole(string $role){
        //
    }
}
