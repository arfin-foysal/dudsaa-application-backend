<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Event;
use App\Models\EventPhoto;
use Illuminate\Http\Request;


class EventController extends Controller
{
    use HelperTrait;
    public function eventList()
    {
        $event = Event::leftJoin('users', 'events.created_by', 'users.id')
            ->select('events.*', 'users.name as publisher_name')
            ->latest()
            ->get();
        return $this->apiResponse($event, 'Event List', true, 200);
    }


    public function eventDetails($id)
    {
        $event = EventPhoto::leftJoin('events', 'event_photos.event_id', 'events.id')
            ->leftJoin('users', 'events.created_by', 'users.id')
            ->select(
                'event_photos.*',
                'events.title',
                'events.description',
                'events.venue',
                'events.event_date',
                'events.created_by',
                'users.name as publisher_name'
            )
            ->where('event_photos.event_id', $id)
            ->latest()
            ->get();
        return $this->apiResponse($event, 'Event Details', true, 200);
    }

    public function eventSaveOrUpdate()
    {

    }

}
