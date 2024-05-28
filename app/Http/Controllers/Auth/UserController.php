<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            $user->assignRole('user');
            
            return response()->json([
                'message' => 'Your account is registered',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
            ], 500);
        }
    }
    
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
            
            $user = User::where('email', $request->email)->first();
            
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }
            
            $token =  $user->createToken($user->email)->plainTextToken;
    
            return response()->json([
                'token' => $token
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->delete();
            
            return response()->json([
                'message' => 'Logged out'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
            ], 500);
        }
    }
}
