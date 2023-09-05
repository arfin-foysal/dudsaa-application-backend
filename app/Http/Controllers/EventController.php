<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Event;
use App\Models\EventPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            )
            ->where('event_photos.event_id', $id)
            ->latest()
            ->get();
        $eventName = Event::where('id', $id)->first();

        $data = [
            'event' => $event,
            'event_name' => $eventName->title,
            'event_description' => $eventName->description
        ];
        return $this->apiResponse($data, 'Event Details', true, 200);
    }

    public function eventSaveOrUpdate(Request $request)
    {
        try {
            $event = [
                'title' => $request->title,
                'description' => $request->description,
                'venue' => $request->venue,
                'event_date' => $request->event_date,
                'is_active' => $request->is_active,
            ];
            if (empty($request->id)) {
                $events = Event::create($event);
                $events->update([
                    'created_by' => auth()->user()->id,
                ]);
                if ($request->hasFile('banner_image')) {
                    $events->update([
                        'banner_image' => $this->imageUpload($request, 'banner_image', 'event'),
                    ]);
                }
                return $this->apiResponse([], 'Event Created', true, 200);
            } else {
                $updateJobs= Event::where('id', $request->id)->first();
                $updateJobs->update($event);
                 if ($request->hasFile('banner_image')) {
                     $updateJobs->update([
                         'banner_image' => $this->imageUpload($request, 'banner_image', 'event', $updateJobs->banner_image),
                     ]);
                 }
                return $this->apiResponse([], 'Event Updated', true, 200);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function eventPhotoList(Request $request, $id)
    {
        $event = EventPhoto::where('event_id', $id)
            ->leftJoin('events', 'event_photos.event_id', 'events.id')
            ->leftJoin('users', 'events.created_by', 'users.id')
            ->select(
                'event_photos.*',
            )
            ->latest()
            ->get();
            // event name
            $eventName = Event::where('id', $id)->first();
            $data = [
                'event' => $event,
                'event_name' => $eventName->title,
            ];

        return $this->apiResponse($data, 'Event Photo List', true, 200);
    }

    public function eventPhotoSaveOrUpdate(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse([], $validator->errors()->first(), false, 403);
            }

            $event = [
                'event_id' => $request->event_id,
                'is_active' => $request->is_active,
            ];


            if (empty($request->id)) {
                $event['image'] = $this->imageUpload($request, 'image', 'event');
                EventPhoto::create($event);
                return $this->apiResponse([], 'Event Photo Created', true, 200);
            } else {
                $photo = EventPhoto::where('id', $request->id)->first();
                if ($request->image) {
                    $event['image'] = $this->imageUpload($request, 'image', 'event', $photo->image);
                }
                $photo->update($event);

                return $this->apiResponse([], 'Event Photo Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function eventPhotoDelete($id)
    {
        try {
            $photo = EventPhoto::where('id', $id)->first();
            $photo->delete();
            return $this->apiResponse([], 'Event Photo Deleted', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
