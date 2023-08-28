<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use HelperTrait;
    public function jobList(Request $request)
    {
        $title = $request->query('title');
        $job = Job::leftJoin('users', 'jobs.created_by', 'users.id')
            ->select(
                'jobs.*',
                'users.name as publisher_name'
            )
            ->when($title, function ($query, $title) {
                return $query->where('jobs.title', 'like', '%' . $title . '%');
            })
            ->latest()
            ->get();
        return $this->apiResponse($job, 'Job List', true, 200);
    }

    //job details
    public function jobDetails($id)
    {
        $job = Job::leftJoin('users', 'jobs.created_by', 'users.id')
            ->select(
                'jobs.*',
                'users.name as publisher_name'
            )
            ->where('jobs.id', $id)
            ->latest()
            ->first();
        return $this->apiResponse($job, 'Job Details', true, 200);
    }


    public function saveOrUpdateJob(Request $request)
    {
        try {

            //validation
            $validateJob = $request->validate([
                'title' => 'required',
                'description' => 'required',
            ]);

            $jobs = [
                'title' => $request->title,
                'company_name' => $request->company_name,
                'position' => $request->position,
                'vacancy' => $request->vacancy,
                'link' => $request->link,
                'location' => $request->location,
                'job_nature' => $request->job_nature,
                'remuneration' => $request->remuneration,
                'description' => $request->description,
                'created_by' => auth()->user()->id,
                'is_active' => $request->is_active,
            ];
            if (empty($request->id)) {
                $job = Job::create($jobs);
                if ($request->hasFile('image')) {
                    $job->update([
                        'image' => $this->imageUpload($request, 'image', 'profile'),
                    ]);
                }

                return $this->apiResponse([], 'Job Created', true, 200);
            } else {
               $updateJobs= Job::where('id', $request->id)->first();
               $updateJobs->update($jobs);
                if ($request->hasFile('image')) {
                    $updateJobs->update([
                        'image' => $this->imageUpload($request, 'image', 'profile', $updateJobs->image),
                    ]);
                }

                return $this->apiResponse([], 'Job Updated', true, 200);
            }
        } catch (\Exception $e) {
            return $this->apiResponse([], $e->getMessage(), false, 500);
        }
    }
}
