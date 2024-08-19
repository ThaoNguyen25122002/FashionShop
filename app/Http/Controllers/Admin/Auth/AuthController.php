<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request){
        if (Auth::attempt($request->only(['email','password']))) {
            $user = User::where('email', $request->email)->first();
            if ($user->role === 'admin') {
                $token = $user->createToken('token')->plainTextToken;
                return response()->json([
                    'message' => 'Login successful',
                    'token' => $token,
                    'user' => $user,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'You are not an admin.',
                ], 403);
            }
        }
        return response()->json([
            'message' => 'Email or password does not exist',
        ], 401);
    }
    public function logout(Request $request)
    {
        // dd($request->user());
        $request->user()->tokens()->delete();

        return response()->noContent();
    }
}
