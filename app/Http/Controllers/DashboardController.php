<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\BloodRequest;
use App\Models\Event;
use App\Models\Job;
use App\Models\Member;
use App\Models\Notice;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use HelperTrait;

    public function dashboard()
    {
        $totalMember = Member::where('status', 'Active')->count();
        $totalNotice = Notice::count();
        $totalJob = Job::count();
        $totalEvent = Event::count();

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

        $data = [
            'total_member' => $totalMember,
            'total_notice' => $totalNotice,
            'total_job' => $totalJob,
            'total_event' => $totalEvent,
            'blood_request' => $blood,
       
        ];
        return $this->apiResponse($data, 'Dashboard Data', true, 200);
    }
}
