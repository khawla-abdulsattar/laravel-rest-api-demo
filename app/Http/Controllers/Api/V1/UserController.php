<?php
// app/Http/Controllers/Api/V1/UserController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;




class UserController extends Controller
{
    // get all users
    public function index()
    {
        $users = User::with('roles')->get();
        return response()->json($users, 200);
    }

    // create a new user with applying a role admin or user
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

        //assin a role to the user
        $user->assignRole($validated['role']);

        //create a token for the user
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully',
            'data'    => (new UserResource($user))->withToken($token), //send a token
        ], 201);
    }

    // get a user
    public function show($id)
    {
        $user = User::with('roles')->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json([
            'status'  => 'success',
            'message' => 'User retrieved successfully',
            'data'    => new UserResource($user), // use Resource json data
        ], 200);
    }

    // update the user data
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
            'data'    => new UserResource($user), // Resource for json data
        ], 200);
    }

    // delete a user
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
