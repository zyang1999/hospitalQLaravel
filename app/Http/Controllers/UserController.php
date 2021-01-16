<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
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
                'user' => $newUser,
                'token' => $token               
            ]);
        }
    }

    public function getUser(Request $request){
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function storeVerificationCredential(Request $request){
        
        $user = $request->user();

        $message = [
            'IC_no.required' => 'The IC Number field is required.',
            'IC_image.required' => 'The IC Image is required.' 
        ];
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'telephone' => 'required',
            'gender' => 'required',
            'IC_no' => 'required',
            'IC_image' => 'required',
        ], $message);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }else{
            $IC_image = base64_decode($request->IC_image);
            $imageDirectory = 'images/' . date('mdYHis') . uniqid(). '.png';
            Storage::put($imageDirectory , $IC_image);
        
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->telephone = $request->telephone;
            $user->gender = $request->gender;
            $user->IC_no = $request->IC_no;
            $user->IC_image = $imageDirectory;

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Credential are waiting to be verified.'    
            ]);
        }
    }

    public function storeSelfie(Request $request){
        $user = $request->user();
        $message = [
            'selfie.required' => 'Please take a photo of yourself.'
        ];

        $validator = Validator::make($request->all(),[
            'selfie' => 'required',
            'status' => 'required'
        ], $message);     

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }else{
            $selfie = base64_decode($request->selfie);
            $imageDirectory = 'images/' . date('mdYHis') . uniqid(). '.png';
            Storage::put($imageDirectory, $selfie);
            
            $user->selfie = $imageDirectory;   
            $user->status = $request->status;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Credential are waiting to be verified.'
            ]);
        }
    }
}

