<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EventController extends Controller
{
    use HelperTrait;
    public function eventList()
    {
        $event = Event::leftJoin('users', 'events.created_by', 'users.id')
            ->select('events.*', 'users.name as publisher_name')
            ->latest()
            ->get();
        return $this->apiResponse($event,'Event List', true, 200);
    }
}
