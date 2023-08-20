<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Division;
use App\Models\State;
use App\Models\Union;
use App\Models\Upazila;
use Illuminate\Http\Request;

class LocationController extends Controller
{
use HelperTrait;
    public function countryList(Request $request)
    {
        //$user_id = $request->user()->id;

        $country = Country::select('id', 'name')->get();

        return $this->apiResponse($country, 'Country List', true, 200);
    }

    public function divisionList(Request $request)
    {
        //$user_id = $request->user()->id;

        $division = Division::all();

        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => $division
        ], 200);
    }

  
    public function stateListByID(Request $request)
    {
        $country = $request->country_id? $request->country_id : 0;
        $state = State::where('country_id', $country)
            ->select('id', 'name','country_id')
            ->get();

        return $this->apiResponse($state, 'State List', true, 200);
    }

    public function cityListByID(Request $request)
    {
        $state = $request->state_id? $request->state_id : 0;
        $city = City::where('state_id', $state)
            ->select('id', 'name','state_id')
            ->get();

        return $this->apiResponse($city, 'City List', true, 200);
    }



  

}
