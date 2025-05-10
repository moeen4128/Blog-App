<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try{
            $registerUserData = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|string|in:admin,user',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::create([
            'firstname' => $registerUserData['firstname'],
            'lastname' => $registerUserData['lastname'],
            'email'=> $registerUserData['email'],
            'password'=> Hash::make($registerUserData['password']),
            'role'=> $registerUserData['role'],
            'profile_picture' => isset($registerUserData['profile_picture']) && $request->hasFile('profile_picture')
                ? $request->file('profile_picture')->store('profile_pictures', 'public')
                : null,
        ]);


        return response()->json([
            'message' => 'User Created',
            'user' => $user,
        ]);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'Error occurred during registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $loginUserData['email'])->first();

        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'token' => $token,
            'user' => [
                'fullname' => $user->firstname . ' ' . $user->lastname,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}
