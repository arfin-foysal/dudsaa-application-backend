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

    public function mobileDashboard(){
        $event = Event::where('is_active', 1)->latest()->first();
        $notice = Notice::where('is_active', 1)->latest()->first();
        $blood = BloodRequest::where('needed_within_date', '>=', date('Y-m-d'))->latest()->first();
        if($event){
            $newEvent = new \stdClass();
            $newEvent->id= $event->id;
            $newEvent->title= $event->title;
            $newEvent->location= $event->location;
            $newEvent->date= $event->date;
            $newEvent->time= $event->time;
            $newEvent->group= $event->group;
            $newEvent->date_time= $event->created_at;
            $newEvent->type = 'event';
        }
        if($notice){
            $newNotice = new \stdClass();
            $newNotice->id= $notice->id;
            $newNotice->title= $notice->title;
            $newNotice->location= null;
            $newNotice->date= null;
            $newNotice->time= null;
            $newNotice->group= null;
            $newNotice->date_time= $notice->created_at;
            $newNotice->type = 'notice';
        }

        if($blood){
            $newBlood = new \stdClass();
            $newBlood->id= $blood->id;
            $newBlood->title= null;
            $newBlood->location= $blood->location;
            $newBlood->date= $blood->needed_within_date;
            $newBlood->time=$blood->needed_within_time;
            $newBlood->group= $blood->blood_group;
            $newBlood->date_time= $blood->created_at;
            $newBlood->type = 'blood';
        }

        $summery=[
            $newEvent,
            $newNotice,
            $newBlood,
        ];

        return $this->apiResponse($summery, 'Dashboard Data', true, 200);

    }


}
