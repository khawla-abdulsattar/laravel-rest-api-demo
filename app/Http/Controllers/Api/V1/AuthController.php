<?php

// app/Http/Controllers/Api/V1/AuthController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;



class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);


        if (!Role::where('name', 'user')->exists()) {
            Role::create(['name' => 'user']);
        }
        $user->assignRole('user');

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'User registered successfully',
            'data'    => (new UserResource($user))->withToken($token),
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'User logged in successfully',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'), // إضافة الصلاحيات
                'token' => $token,
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // حذف جميع التوكنات
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}

