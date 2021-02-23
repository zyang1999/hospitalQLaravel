<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Specialty;
use App\Models\AppointmentFeedback;
use App\Services\Mail;
use App\Services\FCMCloudMessaging;
use Carbon\Carbon;

class UserController extends Controller
{
    protected $mail;
    protected $FCMCloudMessaging;

    public function __construct(
        Mail $mail,
        FCMCloudMessaging $FCMCloudMessaging
    ) {
        $this->mail = $mail;
        $this->FCMCloudMessaging = $FCMCloudMessaging;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "type" => "validation",
                "message" => $validator->messages(),
            ]);
        } else {
            $user = User::where("email", $request->email)->first();
            if ($user == null) {
                return response()->json([
                    "success" => false,
                    "type" => "invalid",
                    "message" =>
                        "Incorrect email address or password, please try again.",
                ]);
            }
            if ($user->role != "ADMIN") {
                if ($user->email_verified_at == "VERIFIED") {
                    if (!Hash::check($request->password, $user->password)) {
                        return response()->json([
                            "success" => false,
                            "type" => "invalid",
                            "message" =>
                                "Incorrect email address or password, please try again.",
                        ]);
                    }
                    $token = $user->createToken($request->email)
                        ->plainTextToken;
                    return response()->json([
                        "success" => true,
                        "user" => $user,
                        "token" => $token,
                    ]);
                } else {
                    return response()->json([
                        "success" => false,
                        "type" => "invalid",
                        "message" =>
                            "Please verify your account with the link included in the verification email sent.",
                    ]);
                }
            } else {
                return response()->json([
                    "success" => false,
                    "type" => "invalid",
                    "message" =>
                        "Incorrect email address or password, please try again.",
                ]);
            }
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->fcm_token = null;
        $user->save();

        return response()->json([
            "user" => $user,
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|unique:users|email",
            "password" =>
                "required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/|confirmed|",
            "password_confirmation" => "required",
        ]);
        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "type" => "validation",
                "message" => $validator->messages(),
            ]);
        } else {
            do {
                $emailToken = Str::random(40);
                $user = User::where("email_verified_at", $emailToken)->first();
            } while ($user);

            $user = User::create([
                "email" => $request->email,
                "password" => Hash::make($request->password),
                "role" => $request->role,
                "email_verified_at" => $emailToken,
            ]);

            $data = [
                "email" => $user->email,
                "url" => config("app.url") . "verifyEmail/" . $emailToken,
            ];
            $this->mail->sendMail(
                $user,
                "email_verification",
                "Email Verification",
                $data
            );

            return response()->json([
                "success" => true,
                "user" => $user,
                "message" =>
                    "A verification email has been sent to your email. Please verify your email.",
            ]);
        }
    }

    public function getUser(Request $request)
    {
        return response()->json([
            "user" => $request->user()->load(["specialty"]),
        ]);
    }

    public function storeVerificationCredential(Request $request)
    {
        $user = $request->user();

        $message = [
            "IC_no.required" => "The IC Number field is required.",
            "IC_no.unique" =>
                "This IC Number is already registered. If this is your IC number, you may ask our admin to create an account for you.",
            "IC_no.digits_between" => "The IC Number must be 12 digits",
            "IC_image.required" => "The IC Image is required.",
        ];

        $validator = Validator::make(
            $request->all(),
            [
                "first_name" => "required",
                "last_name" => "required",
                "telephone" => "required|digits_between:10,11",
                "gender" => "required",
                "homeAddress" => "required",
                "IC_no" => "required|unique:users|digits_between:12,12",
                "IC_image" => "required",
            ],
            $message
        );

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->messages(),
            ]);
        } else {
            $IC_image = base64_decode($request->IC_image);
            $imageDirectory = "images/" . date("mdYHis") . uniqid() . ".png";
            Storage::put($imageDirectory, $IC_image);

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->telephone = $request->telephone;
            $user->gender = $request->gender;
            $user->home_address = $request->homeAddress;
            $user->IC_no = $request->IC_no;
            $user->IC_image = $imageDirectory;

            $user->save();

            return response()->json([
                "success" => true,
                "message" => "Credential are waiting to be verified.",
            ]);
        }
    }

    public function storeSelfie(Request $request)
    {
        $user = $request->user();
        $message = [
            "selfie.required" => "Please take a photo of yourself.",
        ];

        $validator = Validator::make(
            $request->all(),
            [
                "selfie" => "required",
                "status" => "required",
            ],
            $message
        );

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->messages(),
            ]);
        } else {
            $selfie = base64_decode($request->selfie);
            $imageDirectory = "images/" . date("mdYHis") . uniqid() . ".png";
            Storage::put($imageDirectory, $selfie);

            $user->selfie = $imageDirectory;
            $user->status = "VERIFYING";
            $user->save();

            return response()->json([
                "success" => true,
                "message" => "Credential are waiting to be verified.",
            ]);
        }
    }

    public function getDoctorList(Request $request)
    {
        $doctors = User::where("role", "DOCTOR")
            ->where("status", "VERIFIED")
            ->get();

        if ($request->specialty != "All") {
            $doctors = User::whereHas("specialty", function (
                Builder $query
            ) use ($request) {
                $query->where("specialty", $request->specialty);
            })
                ->where("status", "VERIFIED")
                ->get();
        }

        return response()->json([
            "doctors" => $doctors,
        ]);
    }

    public function getHistory(Request $request)
    {
        $queue = $request->user()->getQueueHistory();
        $appointments = $request->user()->getAppointmentHistory();
        $history = $queue
            ->merge($appointments)
            ->sortByDesc("updated_at")
            ->values()
            ->all();
        return response()->json([
            "history" => $history,
        ]);
    }

    public function changePassword(Request $request)
    {
        $rules =
            'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/|confirmed';

        $validator = Validator::make($request->all(), [
            "oldPassword" => "required",
            "newPassword" => $rules,
            "newPassword_confirmation" => "required",
        ]);

        if ($validator->fails()) {
            $response = [
                "success" => false,
                "message" => $validator->messages(),
            ];
        } else {
            $user = $request->user();

            if (Hash::check($request->oldPassword, $user->password)) {
                $user->password = Hash::make($request->newPassword);
                $user->save();

                $response = [
                    "success" => true,
                    "message" => "Password is changed successfully!",
                ];
            } else {
                $response = [
                    "success" => false,
                    "message" => "Your old password is incorrect",
                ];
            }
        }

        return response()->json($response);
    }

    public function changeProfileImage(Request $request)
    {
        $image = base64_decode($request->image);
        $imageDirectory = "images/" . date("mdYHis") . uniqid() . ".png";
        Storage::put($imageDirectory, $image);

        $user = $request->user();
        $user->selfie = $imageDirectory;
        $user->save();

        return response()->json([
            "message" => "Profile picture is changed successfully!",
        ]);
    }

    public function changePhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "telephone" => "required|digits_between:10,11",
        ]);

        if ($validator->fails()) {
            $response = [
                "success" => false,
                "message" => $validator->messages(),
            ];
        } else {
            $user = $request->user();
            $user->telephone = $request->telephone;
            $user->save();

            $response = [
                "success" => true,
                "message" => "Phone number is changed successfully!",
            ];
        }

        return response()->json($response);
    }

    public function changeHomeAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "homeAddress" => "required",
        ]);

        if ($validator->fails()) {
            $response = [
                "success" => false,
                "message" => $validator->messages(),
            ];
        } else {
            $user = $request->user();
            $user->home_address = $request->homeAddress;
            $user->save();

            $response = [
                "success" => true,
                "message" => "Home Address is changed successfully!",
            ];
        }
        return response()->json($response);
    }

    public function saveFcmToken(Request $request)
    {
        $user = $request->user();
        $user->fcm_token = $request->token;
        $user->save();

        return response()->json([
            "token" => $user->fcm_token,
        ]);
    }

    public function verifyEmail($token)
    {
        $user = User::where("email_verified_at", $token)->first();
        if ($user != null) {
            $user->email_verified_at = "VERIFIED";
            $user->save();
        }
        return view("email_verification");
    }

    public function getUsers()
    {
        $users = User::where("status", "VERIFIED")->get();

        return view("users", ["users" => $users]);
    }

    public function getUserDetails($id)
    {
        $user = User::find($id)->load("specialty");

        return response()->json([
            "user" => $user,
        ]);
    }

    public function getUnverifiedPatients()
    {
        return view("verification", [
            "patients" => User::where("status", "VERIFYING")->get(),
        ]);
    }

    public function getUserWithIC($ic)
    {
        return response()->json([
            "patient" => User::where("IC_no", $ic)
                ->where("status", "VERIFIED")
                ->first(),
        ]);
    }

    public function approveAccount(Request $request)
    {
        $user = User::find($request->id);
        $user->status = "VERIFIED";
        $user->save();

        $data = [
            "email" => $user->email,
        ];

        $this->mail->sendMail(
            $user,
            "approve_verification",
            "Account Verification Approved",
            $data
        );

        $this->FCMCloudMessaging->sendFCM(
            $user->fcm_token,
            "Verification Success",
            "Your account is verified. You may start using the apps!",
            ["route" => "Patient", "type" => "Verification"]
        );

        return response()->json([
            "success" => true,
            "message" => "The user verification is approved.",
        ]);
    }

    public function rejectAccount(Request $request)
    {
        $user = User::find($request->id);

        Storage::delete([$user->IC_image, $user->selfie]);
        $user->status = "UNVERIFIED";
        $user->first_name = null;
        $user->last_name = null;
        $user->telephone = null;
        $user->gender = null;
        $user->home_address = null;
        $user->IC_no = null;
        $user->IC_image = null;
        $user->selfie = null;
        $user->save();

        $data = [
            "email" => $user->email,
        ];

        $this->mail->sendMail(
            $user,
            "reject_verification",
            "Account Verification Rejected",
            $data
        );

        $this->FCMCloudMessaging->sendFCM(
            $user->fcm_token,
            "Verification Failed",
            "Your account has failed to be verified based on the provided information.",
            ["route" => "ICVerification", "type" => "VerificationFail"]
        );

        return response()->json([
            "success" => true,
            "message" => "The user verification is rejected.",
        ]);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "email" => "unique:users",
                "IC_no" => "unique:users|digits_between:12,12",
                "telephone" => "digits_between:10,11",
            ],
            [
                "IC_no.unique" => "The IC number has been taken.",
                "IC_no.digits_between" =>
                    "The IC number should be exactly 12 characters long.",
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->messages(),
            ]);
        }

        $path = $request->image->store("images");

        $user = User::create([
            "first_name" => $request->firstName,
            "last_name" => $request->lastName,
            "gender" => $request->gender,
            "telephone" => $request->telephone,
            "home_address" => $request->homeAddress,
            "IC_no" => $request->IC_no,
            "status" => "VERIFIED",
            "role" => $request->role,
            "email" => $request->email,
            "password" => Hash::make($request->IC_no),
            "email_verified_at" => "VERIFIED",
            "selfie" => $path,
        ]);

        if ($request->role == "DOCTOR") {
            $specialty = new Specialty();
            $specialty->specialty = $request->specialty;
            $specialty->location = $request->location;
            $user->specialty()->save($specialty);
        } elseif ($request->role == "NURSE") {
            $specialty = new Specialty();
            $specialty->specialty = "Pharmacist";
            $specialty->location = $request->counterNo;
            $user->specialty()->save($specialty);
        }

        return response()->json([
            "success" => true,
            "message" => "New User is added successfully!",
        ]);
    }

    public function editUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "telephone" => "digits_between:10,11",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->messages(),
            ]);
        }

        $user = User::find($request->id);
        if ($request->role == "ADMIN" || $request->role == "PATIENT") {
            if ($user->role == "DOCTOR" || $user->role == "NURSE") {
                $user->specialty->delete();
            }
        }

        $user->first_name = $request->firstName;
        $user->last_name = $request->lastName;
        $user->telephone = $request->telephone;
        $user->role = $request->role;
        $user->home_address = $request->homeAddress;

        if ($user->specialty == []) {
            $specialty = new Specialty();
        } else {
            $specialty = $user->specialty;
        }
        $specialty->doctor_id = $user->id;

        if ($request->role == "DOCTOR") {
            $specialty->specialty = $request->specialty;
            $specialty->location = $request->location;
            $specialty->save();
        }

        if ($request->role == "NURSE") {
            $specialty->specialty = "Pharmacist";
            $specialty->location = $request->counterNo;
            $specialty->save();
        }

        $user->save();

        return response()->json([
            "success" => true,
            "message" => "The profile is updated successfully!",
        ]);
    }

    public function removeUser(Request $request)
    {
        $user = User::find($request->id);
        $user->status = "INACTIVE";
        $user->save();

        if ($user->role == "DOCTOR") {
            $user
                ->doctorAppointments()
                ->where("status", "AVAILABLE")
                ->delete();

            $user
                ->doctorAppointments()
                ->where("status", "BOOKED")
                ->get()
                ->each(function ($appointment) use ($user) {
                    $appointment->status = "CANCELLED";
                    $appointment->save();

                    $feedback = new AppointmentFeedback();
                    $feedback->created_by = $user->id;
                    $feedback->feedback =
                        "This doctor is no longer working in the hospital.";
                    $appointment->feedback()->save($feedback);

                    $token = $appointment->patient->fcm_token;
                    $title = "Appointment";
                    $body =
                        "Your appointment is cancelled as the doctor is no longer working in the hospital.";
                    $data = [
                        "tab" => "HistoryStack",
                        "screen" => "AppointmentDetails",
                        "appointmentId" => $appointment->id,
                    ];

                    $this->FCMCloudMessaging->sendFCM(
                        $token,
                        $title,
                        $body,
                        $data
                    );
                });
        }

        return response()->json([
            "message" => "This account is set as inactive!",
        ]);
    }

    public function getDoctorsWeb(Request $request)
    {
        $doctors = User::whereHas("specialty", function (Builder $query) use (
            $request
        ) {
            $query->where("specialty", $request->specialty);
        })
            ->where("status", "VERIFIED")
            ->get();

        return view("/components/doctor-select", ["doctors" => $doctors]);
    }

    public function getDoctorSpecialtyWeb(Request $request)
    {
        return response()->json([
            "specialty" => User::find($request->doctorId)->specialty->specialty,
        ]);
    }

    public function changePasswordWeb(Request $request)
    {
        $request->validate(
            [
                "oldPassword" => "required",
                "newPassword" =>
                    "required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/|confirmed|",
                "newPassword_confirmation" => "required",
            ],
            [
                "newPassword.regex" =>
                    "Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character",
            ]
        );

        $user = Auth::user();
        if (Hash::check($request->oldPassword, $user->password)) {
            $user->password = Hash::make($request->newPassword);
            $user->save();

            return redirect("password")->with("status", "Password updated!");
        } else {
            return redirect("/password")->withErrors([
                "oldPassword" => "The old password is incorrect!",
            ]);
        }
    }
}
