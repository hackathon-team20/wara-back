<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserRegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('user_token',['user'])->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        $email = $request->email;
        
        try {
            $user = User::where('email', $email)->first();

            if ($user) {
                return response()->json(['exists' => true], 200);
            } else {
                return response()->json(['exists' => false], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error checking email: ' . $e->getMessage()], 500);
        }
    }
}
