<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use HelperTrait;

    public function userSaveOrCreate(Request $request)
    {
        try {
            $user = [
                'name' => $request->name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'batch_no' => $request->batch_no,

            ];

            if (empty(request()->id)) {

                $validateUser = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'email' => 'required|email|unique:users',
                        'contact_no' => 'required|unique:users',
                        'password' => 'required',
                        'gender' => 'required',
                        'batch_no' => 'required',
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
                $createUser = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'password' => bcrypt($request->password),
                    'user_type' => $request->user_type ? $request->user_type : "Member",
                ]);
                if ($request->hasFile('image')) {
                    $createUser->update([
                        'image' => $this->imageUpload($request, 'image', 'profile'),
                    ]);
                }
                $member = Member::create($user);
                $member->update([
                    'user_id' => $createUser->id,
                    'image' => $createUser->image,
                ]);
                return $this->apiResponse([], 'Member Created', true, 200);
            } else {
                $updateUser = User::where('id', $request->id)->first();
                $validateUser = Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
                        'email' => 'required|email|unique:users,email,' . $updateUser->id,
                        'contact_no' => 'required|unique:users,contact_no,' . $updateUser->id,
                    ]
                );

                if ($validateUser->fails()) {
                    return $this->apiResponse($validateUser->errors(), 'validation error', false, 422);
                }

                $updateUser->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                ]);

                if ($request->hasFile('image')) {
                    $updateUser->update([
                        'image' => $this->imageUpload($request, 'image', 'profile'),
                    ]);
                }
                $member = Member::where('user_id', $request->id)->update($user);
                return $this->apiResponse([], 'Member Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function userList(Request $request)
    {
        $user = Member::leftJoin('users', 'users.id', '=', 'members.user_id')

            ->select(
                'users.*',
                'members.id as member_id',
                'members.batch_no',
                'members.date_of_birth',
                'members.gender',
            )


            ->latest()
            ->get();

        return $this->apiResponse($user, 'User List', true, 200);
    }

    public function userActiveInactive(Request $request)
    {
        try {
            $user = User::where('id', $request->id)->update([
                'is_active' => $request->is_active,
            ]);
            Member::where('user_id', $request->id)->update([
                'is_active' => $request->is_active,
            ]);
            return $this->apiResponse([], 'User Updated', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $user = User::where('id', $request->id)->update([
                'password' => bcrypt($request->password),
            ]);
            return $this->apiResponse([], 'Password Updated', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }

    }
}
