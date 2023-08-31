<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Donation;
use App\Models\Member;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    use HelperTrait;
    public function donationList()
    {
        $donation = Donation::first();
        return $this->apiResponse($donation, 'Donation List', true, 200);
    }

    public function donationSaveOrUpdate(Request $request)
    {
        try {
            $donation = [
                'title' => $request->title,
                'details' => $request->details,
            ];
            if (empty($request->id)) {
                Donation::create($donation);
                return $this->apiResponse([], 'Donation Created', true, 200);
            } else {
                Donation::where('id', $request->id)->update($donation);
                return $this->apiResponse([], 'Donation Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

}
