<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    //
    public function index()
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    public function create(RegisterRequest $request)
    {
        $data = $request->all();
        // return $data;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public_uploads');
            $data['avatar'] = 'uploads/'.$path;
        }
        $data['password'] = Hash::make($request->password);
        $user = User::create($data);

        return response()->json(['message' => 'Đăng ký thành công!', 'user' => $user], 201);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return new UserResource($user);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'name' => 'required|string|max:255',
            'role' => 'required|in:member,admin',
            'newAvatar' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 402);
        }
        $user = User::findOrFail($id);
        $data = $request->only(['name', 'email', 'phone', 'role', 'ward', 'city', 'district', 'street_address']);
        // $data['avatar'] = 'hehe';
        if ($request->hasFile('newAvatar')) {
            $urlCheck = str_replace('uploads/', '', $user->avatar);
            if ($user->avatar && Storage::disk('public_uploads')->exists($urlCheck)) {
                Storage::disk('public_uploads')->delete($urlCheck);
            }
            $file = $request->file('newAvatar');
            $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public_uploads');
            $data['avatar'] = 'uploads/'.$path;
        }
        // dd($data);
        $user->update($data);

        return response()->noContent();
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->noContent();
    }
}
