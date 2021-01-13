<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' =>false,
                'type' => 'validation',
                'message' =>$validator->messages()]);
        }else{
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'type' => 'invalid',
                    'message' => 'Incorrect email address or password, please try again.'
                ]);
            }
            $token = $user->createToken($request->email)->plainTextToken;
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
                ]);
        }      
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' =>false,
                'type' => 'validation',
                'message' =>$validator->messages()]);
        }else{
            $newUser = new User;
            $newUser->email = $request->email;
            $newUser->password = Hash::make($request->password);
            $newUser->role = $request->role;
            $newUser->save();
            
            $token = $newUser->createToken($request->email)->plainTextToken;

            return response()->json([
                'success' => true,
                'role' => $newUser->role,
                'token' => $token               
            ]);
        }
    }

    public function getUser(Request $request){
        return response()->json([
            'user' => $request->user()
        ]);
    }
}
