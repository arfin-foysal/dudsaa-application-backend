<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Education_information;
use App\Models\Member;
use App\Models\Service_information;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;

class MemberController extends Controller
{
    use HelperTrait;
    public function alumniList(Request $request)
    {
        $text = $request->query('text');

        $alumni = Member::where('members.status', 'Active')
            ->where('members.is_active', 1)
            ->leftJoin('users', 'users.id', '=', 'members.user_id')
            ->leftJoin('countries', 'countries.id', '=', 'members.country_id')
            ->leftJoin('states', 'states.id', '=', 'members.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'members.city_id')
            // like search member name department batch country state city
            ->where('members.name', 'like', '%' . $text . '%')
            ->orWhere('members.department', 'like', '%' . $text . '%')
            ->orWhere('members.batch_no', 'like', '%' . $text . '%')
            ->orWhere('countries.name', 'like', '%' . $text . '%')
            ->orWhere('states.name', 'like', '%' . $text . '%')
            ->orWhere('cities.name', 'like', '%' . $text . '%')
            ->select(
                'members.*',
                'countries.name as country_name',
                'states.name as state_name',
                'cities.name as city_name',
                'users.image as user_image',
            )
            ->get();

        return $this->apiResponse($alumni, 'Alumni List', true, 200);
    }

    public function alumniDetails(Request $request, $id)
    {
        $alumni = Member::where('members.id', $id)
            ->leftJoin('users', 'users.id', '=', 'members.user_id')
            ->leftJoin('countries', 'countries.id', '=', 'members.country_id')
            ->leftJoin('states', 'states.id', '=', 'members.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'members.city_id')
            ->select('members.*', 'users.image as user_image', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name')
            ->first();
        return $this->apiResponse($alumni, 'Alumni Details', true, 200);
    }

    public function sameBatchAlumniList(Request $request)
    {
        $batch = auth()->user()->id;
        $batch = Member::where('user_id', $batch)->first()->batch_no;
        $alumni = Member::where('status', 'Active')
            ->where('batch_no', $batch)
            ->where('members.id', '!=', auth()->user()->id)
            ->leftJoin('users', 'users.id', '=', 'members.user_id')
            ->leftJoin('countries', 'countries.id', '=', 'members.country_id')
            ->leftJoin('states', 'states.id', '=', 'members.state_id')
            ->leftJoin('cities', 'cities.id', '=', 'members.city_id')
            ->select('members.*', 'users.image as user_image', 'countries.name as country_name', 'states.name as state_name', 'cities.name as city_name')
            ->get();
        return $this->apiResponse($alumni, 'Alumni List', true, 200);
    }

    public function educationSaveOrUpdate(Request $request)
    {
        try {
            $request->validate([
                'standard' => 'required',
                'institution' => 'required',
                'status' => 'required',
                'is_active' => 'required',
            ]);

            $education = [
                'member_id' => auth()->user()->id,
                'standard' => $request->standard,
                'institution' => $request->institution,
                'passing_year' => $request->passing_year,
                'result' => $request->result,
                'status' => $request->status,
                'is_active' => $request->is_active,
            ];
            if (empty($request->id)) {
                Education_information::create($education);
                return $this->apiResponse([], 'Education Created', true, 200);
            } else {
                Education_information::where('id', $request->id)->update($education);
                return $this->apiResponse([], 'Education Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function educationList(Request $request)
    {
        $education = Education_information::where('member_id', auth()->user()->id)
            ->get();
        return $this->apiResponse($education, 'Education List', true, 200);
    }
    public function educationListById(Request $request, $id)
    {
        $education = Education_information::where('member_id', auth()->user()->id)
            ->where('id', $id)
            ->get();
        return $this->apiResponse($education, 'Education List', true, 200);
    }

    public function serviceSaveOrUpdate(Request $request)
    {
        try {
            $request->validate([
                'designation' => 'required',
                'organization' => 'required',
                'start_date' => 'required',
                'is_active' => 'required',
            ]);

            $service = [
                'member_id' => auth()->user()->id,
                'designation' => $request->designation,
                'organization' => $request->organization,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_continue' => $request->is_continue,
                'is_active' => $request->is_active,
            ];
            if (empty($request->id)) {
                Service_information::create($service);
                return $this->apiResponse([], 'Education Created', true, 200);
            } else {
                Service_information::where('id', $request->id)->update($service);
                return $this->apiResponse([], 'Education Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
    public function serviceList(Request $request)
    {
        $education = Service_information::where('member_id', auth()->user()->id)
            ->get();
        return $this->apiResponse($education, 'Education List', true, 200);
    }
    public function serviceListById(Request $request, $id)
    {
        $education = Service_information::where('member_id', auth()->user()->id)
            ->where('id', $id)
            ->get();
        return $this->apiResponse($education, 'Education List', true, 200);
    }
}
