<?php
// app/Http/Controllers/Api/V1/UserController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;




class UserController extends Controller
{
    // عرض جميع المستخدمين مع أدوارهم
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users, 200);
    }

    // إنشاء مستخدم جديد مع تعيين دور (مثل admin أو user)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string|in:user,admin',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $user = User::create($validated);

        // تعيين الدور للمستخدم
        $user->assignRole($validated['role']);

        // إنشاء التوكن
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully',
            'data'    => (new UserResource($user))->withToken($token), // ✅ إرسال التوكن مع المستخدم
        ], 201);
    }

    // استرجاع مستخدم معين
    public function show($id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'status'  => 'success',
            'message' => 'User retrieved successfully',
            'data'    => new UserResource($user), // ✅ تحويل المستخدم إلى Resource
        ], 200);
    }

    // تحديث بيانات مستخدم
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $validated = $request->validate([
            'name'     => 'sometimes|required|string',
            'email'    => 'sometimes|required|email|unique:users,email,'.$id,
            'password' => 'sometimes|required|string|min:6',
            'role'     => 'sometimes|required|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'User updated successfully',
            'data'    => new UserResource($user), // ✅ تحويل المستخدم إلى Resource
        ], 200);
    }

    // حذف مستخدم
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
