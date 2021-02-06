<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Specialty;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'type' => 'validation',
                'message' => $validator->messages()
            ]);
        } else {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
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

    public function register(StoreUserRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'type' => 'validation',
                'message' => $validator->messages()
            ]);
        } else {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

            $token = $user->createToken($request->email)->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ]);
        }
    }

    public function getUser(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    public function storeVerificationCredential(Request $request)
    {

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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {
            $IC_image = base64_decode($request->IC_image);
            $imageDirectory = 'images/' . date('mdYHis') . uniqid() . '.png';
            Storage::put($imageDirectory, $IC_image);

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

    public function storeSelfie(Request $request)
    {
        $user = $request->user();
        $message = [
            'selfie.required' => 'Please take a photo of yourself.'
        ];

        $validator = Validator::make($request->all(), [
            'selfie' => 'required',
            'status' => 'required'
        ], $message);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        } else {
            $selfie = base64_decode($request->selfie);
            $imageDirectory = 'images/' . date('mdYHis') . uniqid() . '.png';
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

    public function getDoctorList(Request $request)
    {

        $doctors = User::where('role', 'DOCTOR')->get();

        if ($request->specialtyId != 'All') {
            $doctors = Specialty::find($request->specialtyId)->user()->get();
        }

        return response()->json([
            'doctors' => $doctors
        ]);
    }

    public function getHistory(Request $request)
    {
        $queue = $request->user()->getQueueHistory();
        $appointments = $request->user()->getAppointmentHistory();
        $history = $queue->merge($appointments)->sortByDesc('updated_at')->values()->all();
        return response()->json([
            'history' => $history
        ]);
    }

    public function changePassword(Request $request)
    {
        $rules = 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/|confirmed';

        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => $rules,
            'newPassword_confirmation' => 'required'
        ]);

        if ($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->messages()
            ];
        }else{
            $user = $request->user();

            if(Hash::check($request->oldPassword, $user->password)){
                $user->password = Hash::make($request->newPassword);
                $user->save();

                $response = [
                    'success' => true,
                    'message' => 'Password is changed successfully!'
                ];
            }else{
                $response = [
                    'success' => false,
                    'message' => 'Your old password is incorrect'
                ];
            }
        }

        return response()->json($response);
    }

    public function changeProfileImage(Request $request)
    {
        $image = base64_decode($request->image);
        $imageDirectory = 'images/' . date('mdYHis') . uniqid() . '.png';
        Storage::put($imageDirectory, $image);

        $user = $request->user();
        $user->selfie = $imageDirectory;
        $user->save();

        return response()->json([
            'message' => 'Profile picture is changed successfully!'
        ]);
    }

    public function changePhoneNumber(Request $request){
        $validator = Validator::make($request->all(),[
            'telephone' => 'required|digits_between:10,11'
        ]);

        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->messages()
            ];
        }else{
            $user = $request->user();
            $user->telephone = $request->telephone;
            $user->save();

            $response = [
                'success' => true,
                'message' => 'Phone number is changed successfully!'
            ];
        }

        return response()->json($response);
    }

    public function saveFcmToken(Request $request){
        $user = $request->user();
        $user->fcm_token = $request->token;
        $user->save();
        
        $this->FCMCloudMessaging->sendFCM($user->fcm_token);

        return response()->json([
            'token' => $user->fcm_token
        ]);
    }
}
