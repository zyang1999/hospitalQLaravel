<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Specialty;
use App\Services\Mail;
use Carbon\Carbon;

class UserController extends Controller
{
    protected $mail;

    public function __construct(Mail $mail){
        $this->mail = $mail;
    }

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

    public function logout(Request $request){
        $user = $request->user();
        $user->fcm_token = null;
        $user->save();

        return response()->json([
            'user' => $user
        ]);
    }

    public function register(Request $request)
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
            do {
                $emailToken = Str::random(40);
                $user = User::where('email_verified_at', $emailToken)->first();
            } while ($user);

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => $emailToken
            ]);
            
            // $token = $user->createToken($request->email)->plainTextToken;
            $data = [
                'email' => $user->email,
                'url' => config('app.url'). 'verifyEmail/' . $emailToken 
            ];
            $this->mail->sendMail($user, 'email_verification', 'Email Verification', $data);

            return response()->json([
                'success' => true,
                'user' => $user,
                'message' => 'A verification email has been sent to your email. Please verify your email.'
                // 'token' => $token
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
            $user->status = 'VERIFYING';
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

        return response()->json([
            'token' => $user->fcm_token
        ]);
    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verified_at', $token)->first();
        if($user != null){
            $user->email_verified_at = 'VERIFIED';
        }   
        return view('email_verification');   
    }

    public function getUsers()
    {
        $users = User::where('status', 'VERIFIED')->get();

        return view('users', ['users' => $users]);
    }

    public function getUserDetails($id)
    {
        $user = User::find($id)->load('specialty');

        return response()->json([
            'user' => $user
        ]);
    }

    public function getUnverifiedPatients()
    {
        return view('verification', ['patients' => User::where('status', 'VERIFYING')->get()]);
    }

    public function getUserWithIC($ic){
        return response()->json([
            'patient' => User::where('IC_no', $ic)->where('status', 'VERIFIED')->first()
        ]);
    }

    public function approveAccount(Request $request)
    {
        $user = User::find($request->id);
        $user->status = 'VERIFIED';
        $user->save();

        $data = [
            'email' => $user->email
        ];

        $this->mail->sendMail($user, 'approve_verification', 'Account Verification Approved', $data);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function rejectAccount(Request $request)
    {
        $user = User::find($request->id);
        $user->status = 'UNVERIFIED';
        $user->save();

        $data = [
            'email' => $user->email
        ];

        $this->mail->sendMail($user, 'reject_verification', 'Account Verification Rejected', $data);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function createUser(Request $request)
    {   
        $validator = Validator::make($request->all(),[
            'email' => 'unique:users',
            'IC_no' => 'unique:users|digits_between:12,12',
            'telephone' => 'digits_between:10,11',
        ], [
            'IC_no.unique' => 'The IC number has been taken.' ,
            'IC_no.digits_between' => 'The IC number should be exactly 12 characters long.'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->messages()
            ]);
        }

        $path = $request->image->store('images');

        $user = User::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'gender' => $request->gender,
            'telephone' => $request->telephone,
            'home_address' => $request->homeAddress,
            'IC_no' => $request->IC_no,
            'status' => 'VERIFIED',
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->ic),
            'email_verified_at' => 'VERIFIED',
            'selfie' => $path
        ]);

        if($request->role == 'DOCTOR'){
            $specialty = new Specialty;
            $specialty->specialty = $request->specialty;
            $specialty->location = $request->location;
            $user->specialty()->save($specialty);
        }

        return response()->json([
            'success' => true,
            'message' => 'New Staff is added successfully!'
        ]);
    }

    public function editUser(Request $request){
        $validator = Validator::make($request->all(),[
            'telephone' => 'digits_between:10,11',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' =>false,
                'message' => $validator->messages()
            ]);
        }

        $user = User::find($request->id);
        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->telephone = $request->telephone;
        $user->role = $request->role;
        $user->home_address = $request->homeAddress;

        if($request->role == 'DOCTOR'){
            $specialty = $user->specialty;
            $specialty->specialty = $request->specialty;
            $specialty->location = $request->location;
            $specialty->save();
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'The profile is updated successfully!'
        ]);
    }

    public function removeUser(Request $request){
        $user = User::find($request->id);
        $user->status = 'INACTIVE';
        $user->save();

        return response()->json([
            'message' => 'This account is set as inactive!'
        ]);
    }

    public function getDoctorsWeb(Request $request){
        $doctors = User::whereHas('specialty', function(Builder $query) use($request){
            $query->where('specialty', $request->specialty);
        })->get();

        return view('/components/doctor-select', ['doctors' => $doctors]);
    }

    public function getDoctorSpecialtyWeb(Request $request)
    {
        return response()->json(['specialty' => User::find($request->doctorId)->specialty->specialty]);
    }
}
