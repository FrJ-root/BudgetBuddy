<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller{
    public function allUsers(Request $request){
        if (!$request->user()) {
            return response()->json(['message' => 'You should login'], 401);
        }

        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'no users found'], 404);
        }

        return response()->json($users);
    }
}