<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminProfileController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        if ($user->avatar !== null) {
            $user->avatar = asset($user->avatar);
        }

        return response()->json(['data' => $user], 200);
    }

    public function update(Request $request)
    {
        // $user = User::where('email',$request->email)->get();
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'new_avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 402);
        }
        // if ($request->hasFile('avatar')) {
        //     if ($user->avatar && Storage::exists($user->avatar)) {
        //         Storage::delete($user->avatar);
        //     }
        //     $file = $request->file('avatar');
        //     $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //     // $path = $file->storeAs('public/avatars', $filename);
        //     // $user->avatar = Storage::url($path);
        //     $path = $file->storeAs('avatars', $filename);
        //     $user->avatar = $path;
        // }
        if ($request->hasFile('new_avatar')) {
            // return response()->json($user->avatar);
            $urlCheck = str_replace('uploads/', '', $user->avatar);
            if ($user->avatar && Storage::disk('public_uploads')->exists($urlCheck)) {
                Storage::disk('public_uploads')->delete($urlCheck);
            }
            $file = $request->file('new_avatar');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public_uploads');
            $user->avatar = 'uploads/'.$path;
        }

        // dd($request->avatar);
        // return $request->all();
        $user->update($request->only(['name', 'phone', 'city', 'district', 'ward', 'street_address']));
        $user->save();

        return response()->json(['data' => $user], 200);
    }
}
