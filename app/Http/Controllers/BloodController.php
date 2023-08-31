<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\BloodRequest;
use App\Models\Member;
use Illuminate\Http\Request;

class BloodController extends Controller
{
    use HelperTrait;
    public function bloodRequest(Request $request)
    {
        try {
            $blood = [
                'user_id' => auth()->user()->id,
                'blood_group' => $request->blood_group,
                'units' => $request->units,
                'hospital_name' => $request->hospital_name,
                'location' => $request->location,
                'number' => $request->number,
                'needed_within_date' => $request->needed_within_date,
                'needed_within_time' => $request->needed_within_time,
                'is_active' => $request->is_active,
            ];
            if (empty($request->id)) {
                BloodRequest::create($blood);
                return $this->apiResponse([], 'Blood Request Created', true, 200);
            } else {
                BloodRequest::where('id', $request->id)->update($blood);
                return $this->apiResponse([], 'Blood Request Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function bloodRequestList(Request $request)
    {
        $blood = BloodRequest::where('needed_within_date', '>=', date('Y-m-d'))
            ->leftJoin('users', 'users.id', '=', 'blood_requests.user_id')
            ->leftJoin('members', 'members.user_id', '=', 'blood_requests.user_id')
            ->select('blood_requests.*', 
            'members.name as user_name',
            'members.image as image',
            'members.batch_no as user_batch_no',
            'members.contact_no as contact_no',

            )
            ->get();
        return $this->apiResponse($blood, 'Blood Request List', true, 200);
    }

    public function ownBloodRequestList(Request $request)
    {
        $blood = BloodRequest::where('blood_requests.user_id', auth()->user()->id)
            ->leftJoin('users', 'users.id', '=', 'blood_requests.user_id')
            ->leftJoin('members', 'members.user_id', '=', 'blood_requests.user_id')
            ->select('blood_requests.*', 
            'members.name as user_name',
            'members.image as image',
            'members.batch_no as user_batch_no',
            'members.contact_no as contact_no',
            )
            ->get();
        return $this->apiResponse($blood, 'Blood Request List', true, 200);
    }

    public function bloodRequestDetails($id)
    {
        $blood = BloodRequest::where('blood_requests.id', $id)
            ->leftJoin('users', 'users.id', '=', 'blood_requests.user_id')
            ->leftJoin('members', 'members.user_id', '=', 'blood_requests.user_id')
            ->select('blood_requests.*', 
            'members.name as user_name',
            'members.image as image',
            'members.batch_no as user_batch_no',
            'members.contact_no as contact_no',
            )
            ->first();
        return $this->apiResponse($blood, 'Blood Request Details', true, 200);
    }

    public function alumniListByBloodGroup(Request $request)
    {
        $bloodGroup =$request->blood_group;
        $alumniList = Member::leftJoin('users', 'users.id', '=', 'members.user_id')
            ->leftJoin('countries', 'countries.id', '=', 'members.country_id')
            ->leftJoin('states', 'states.id', '=', 'members.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'members.city_id')
            ->where('interested_to_donate', 1)
            ->select(
                'members.id',
                'members.name',
                'members.image',
                'members.blood_group',
                'members.batch_no',
                'members.last_blood_donation_date',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
            )->when($bloodGroup, function ($query, $bloodGroup) {
                return $query->where('members.blood_group', $bloodGroup);
            })
            ->get();
        return $this->apiResponse($alumniList, 'Alumni List', true, 200);
    }
}
