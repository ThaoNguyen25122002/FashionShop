<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthMemberController extends Controller
{
    //
    public function login(LoginRequest $request){
        if (Auth::attempt($request->only(['email','password']))) {
            // $user = User::where('email', $request->email)->first();
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);            
        }
        return response()->json([
            'message' => 'Email or password không đúng!',
        ], 401);
    }
}
