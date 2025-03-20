<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller{
    
    public function register(Request $request) {

        URL::forceScheme('https');
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:70',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
         return response()->json([
            'you will take your token when you login, just Smille'
         ], 205);

    }
    
    public function login (Request $request){

        URL::forceScheme('https');

        $request->validate([
            'email.required' => 'please dont let password empty',
            'email.email' => 'The email must be a valid email address.',
            'password.required' => 'please dont let password empty',
            'password.min:8' => 'it must be more than 8 car',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Ooops!!!!'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'youre logged successfully','take your token' => $token,
         ], 205);
    }
    
    public function logout(Request $request) {

        URL::forceScheme('https');

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request) {
        return response()->json($request->user());
    }

}