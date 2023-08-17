<?php

namespace App\Http\Controllers\Api;

use App\Http\Traits\HelperTrait;
use Exception;
use Illuminate\Http\Request;

use App\Models\Member;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

//Notification
// use App\Notifications\SendNotification;
// use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    use HelperTrait;
    public function registerUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'contact_no' => 'required|unique:users',
                    'password' => 'required',
                    'gender' => 'required',
                    'batch_no'=> 'required',
                ]
            );

            if ($validateUser->fails()) {
                return $this->apiResponse($validateUser->errors(), 'validation error', false, 422);
            }

            if ($request->email) {
                $is_exist = User::where('email', $request->email)->first();
                if (!empty($is_exist)) {
                    return $this->apiResponse([], 'Email already been used! Please use another email', false, 422);
                }
            }

            if ($request->contact_no) {
                $is_exist = User::where('contact_no', $request->contact_no)->first();
                if (!empty($is_exist)) {
                    return $this->apiResponse([], 'Contact No already been used! Please use another contact no', false, 422);
                }
            }

        
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'user_type' => $request->user_type ? $request->user_type : "Member",
                'password' => Hash::make($request->password),
            ]);

            if ($request->hasFile('image')) {
                $user->update([
                    'image' => $this->imageUpload($request, 'image', 'profile'),
                ]);
            }
           
            Member::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'gender'=>$request->gender,
                'date_of_birth'=>$request->date_of_birth,
                'batch_no'=>$request->batch_no,
                'is_active' => true
            ]);
        
            $response_user = [
                'name' => $user->name,
                'user_type' => $request->user_type ? $request->user_type : "Member",
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ];

            return $this->apiResponse($response_user, 'User has been registered successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return $this->apiResponse($validateUser->errors(), 'validation error', false, 422);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return $this->apiResponse([], 'Invalid Credentials', false, 401);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user->is_active) {
                return $this->apiResponse([], 'Your account is not active! Please contact with admin', false, 401);
            }

            $response_user = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'image' => $user->image,
                'address' => $user->address,
                'contact_no' => $user->contact_no,
                'updated_at' => $user->updated_at,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ];

            return $this->apiResponse($response_user, 'User has been logged in successfully', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function profileDetailsByID($user_id)
    {
        $user = User::select('users.*', 'countries.country_name')->where('users.id', $user_id)
            ->leftJoin('countries', 'countries.id', 'users.country_id')
            ->first();

        return $user;
    }

    public function saveOrUpdateUser(Request $request)
    {
        try {
            $formData = json_decode($request->data, true);
            if ($formData['id']) {
                $profile_url = null;
                if ($request->hasFile('file')) {
                    $image = $request->file('file');
                    $time = time();
                    $feature_image = "profile_image_" . $time . '.' . $image->getClientOriginalExtension();
                    $destinationProfile = 'uploads/profile';
                    $image->move($destinationProfile, $feature_image);
                    $profile_url = $destinationProfile . '/' . $feature_image;
                }

                User::where('id', $formData['id'])->update([
                    'name' => $formData['name'],
                    'email' => $formData['email'],
                    'contact_no' => $formData['contact_no'],
                    'country_id' => $formData['country_id'],
                    'address' => $formData['address'],
                    'institution' => $formData['institution'],
                    'education' => $formData['education'],
                    'user_type' => $formData['user_type'] ? $formData['user_type'] : "Student"
                ]);

                if ($request->hasFile('file')) {
                    User::where('id', $formData['id'])->update([
                        'image' => $profile_url
                    ]);
                }

                return $this->apiResponse([], 'User has been updated successfully', true, 200);
            } else {
                $isExist = User::where('email', $formData['email'])->first();
                if (empty($isExist)) {
                    $profile_url = null;
                    if ($request->hasFile('file')) {
                        $image = $request->file('file');
                        $time = time();
                        $feature_image = "profile_image_" . $time . '.' . $image->getClientOriginalExtension();
                        $destinationProfile = 'uploads/profile';
                        $image->move($destinationProfile, $feature_image);
                        $profile_url = $destinationProfile . '/' . $feature_image;
                    }

                    $user = User::create([
                        'name' => $formData['name'],
                        'email' => $formData['email'],
                        'contact_no' => $formData['contact_no'],
                        'country_id' => $formData['country_id'],
                        'address' => $formData['address'],
                        'institution' => $formData['institution'],
                        'education' => $formData['education'],
                        'user_type' => $formData['user_type'] ? $formData['user_type'] : "Student"
                    ]);

                    if ($request->hasFile('file')) {
                        User::where('id', $user->id)->update([
                            'image' => $profile_url
                        ]);
                    }

                    return $this->apiResponse([], 'User has been created successfully', true, 200);
                } else {
                    return $this->apiResponse([], 'Email already been used! Please use another email', false, 422);
                }
            }
        } catch (Exception $e) {
            return $this->apiResponse([], $e->getMessage(), false, 500);
        }
    }



    public function updateUser(Request $request)
    {
        $user_id = $request->user()->id;
        try {
            if (!$request->name && !$request->contact_no && !$request->country_id && !$request->address && !$request->institution && !$request->education && !$request->hasFile('image')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please, attach information!',
                    'data' => []
                ], 200);
            }

            $profile_image = null;
            $profile_url = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $time = time();
                $profile_image = "profile_image_" . $time . '.' . $image->getClientOriginalExtension();
                $destinationProfile = 'uploads/profile';
                $image->move($destinationProfile, $profile_image);
                $profile_url = $destinationProfile . '/' . $profile_image;
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
            ]);

            if ($request->name) {
                User::where('id', $user_id)->update([
                    'name' => $request->name
                ]);
            }

            if ($request->contact_no) {
                User::where('id', $user_id)->update([
                    'contact_no' => $request->contact_no
                ]);
            }

            if ($request->country_id) {
                User::where('id', $user_id)->update([
                    'country_id' => $request->country_id
                ]);
            }

            if ($request->address) {
                User::where('id', $user_id)->update([
                    'address' => $request->address
                ]);
            }

            if ($request->institution) {
                User::where('id', $user_id)->update([
                    'institution' => $request->institution
                ]);
            }

            if ($request->education) {
                User::where('id', $user_id)->update([
                    'education' => $request->education
                ]);
            }

            // User::where('id', $user_id)->update([
            //     'name' => $request->name,
            //     'contact_no' => $request->contact_no,
            //     'country_id' => $request->country_id,
            //     'address' => $request->address,
            //     'institution' => $request->institution,
            //     'education' => $request->education
            // ]);

            if ($request->hasFile('image')) {
                User::where('id', $user_id)->update([
                    'image' => $profile_url
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Updated Successful',
                'data' => $this->profileDetailsByID($user_id)
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getProfile(Request $request)
    {
        $user_id = $request->user()->id;
        $user = User::select('users.*', 'countries.country_name')->where('users.id', $user_id)
            ->leftJoin('countries', 'countries.id', 'users.country_id')
            ->first();

        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $user
        ], 200);
    }


    public function deleteUserAccount(Request $request)
    {
        $user_id = $request->user()->id;

        $user = User::where('id', $user_id)->first();

        User::where('id', $user_id)->update([
            "contact_no" => $user_id . "_deleted_" . $user->contact_no,
            "email" => $user_id . "_deleted_" . $user->email,
            "is_active" => false
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Account deleted successful',
            'data' => []
        ], 200);
    }
}
