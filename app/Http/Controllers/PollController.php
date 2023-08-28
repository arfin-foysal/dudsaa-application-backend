<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use Illuminate\Http\Request;

class PollController extends Controller
{
    use HelperTrait;
    public function votingList()
    {
        $polls = Poll::where([['status', 'Active'], ['end_date', '>=', date('Y-m-d')]])
            ->leftJoin('users', 'users.id', '=', 'polls.created_by')
            ->leftJoin('votes', 'votes.polls_id', '=', 'polls.id')
            ->select('polls.*', 'users.name as user_name')
            ->get();
        foreach ($polls as $poll) {
            $poll->options = PollOption::where('polls_id', $poll->id)
                ->get();
        }
        // option is voted check
        foreach ($polls as $poll) {
            foreach ($poll->options as $option) {
                $option->is_voted = false;
                $option->is_voted = Vote::where([['polls_id', $poll->id], ['option_id', $option->id], ['user_id', auth()->user()->id]])->exists();
                $option->vote_count = Vote::where([['polls_id', $poll->id], ['option_id', $option->id]])->count();
            }
        }
        return $this->apiResponse($polls, 'Poll List', true, 200);
    }

    public function voting(Request $request)
    {
        try {
            $request->validate([
                'polls_id' => 'required',
                'option_id' => 'required',
            ]);

            if (Vote::where([['polls_id', $request->polls_id], ['user_id', auth()->user()->id]])->exists()) {
                if (Vote::where('option_id', "!=", $request->option_id)) {
                    $vote = Vote::where([['polls_id', $request->polls_id], ['user_id', auth()->user()->id]])->first();
                    $decrementVote = PollOption::where('id', $vote->option_id)->first();
                    $decrementVote->decrement('votes');
                    $option = PollOption::where('id', $request->option_id)->first();
                    $option->increment('votes');
                    $vote->update([
                        'option_id' => $request->option_id,
                    ]);
                    return $this->apiResponse([], 'Vote Updated', true, 200);
                }
            } else {
                $vote = Vote::create([
                    'polls_id' => $request->polls_id,
                    'option_id' => $request->option_id,
                    'user_id' => auth()->user()->id,
                ]);
                $option = PollOption::where('id', $request->option_id)->first();
                $option->increment('votes');
                return $this->apiResponse([], 'Vote Added', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse(null, $th->getMessage(), false, 500);
        }
    }


    public function pollSaveOrUpdate(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required',
                'end_date' => 'required',
            ]);

            if ($request->id) {
                $poll = Poll::where('id', $request->id)->first();
                $poll->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'end_date' => $request->end_date,
                    'status' => $request->status,
                    'is_active' => $request->is_active,
                ]);

                $option_data = json_decode($request->option, true);

                foreach ($option_data as $option) {
                    if (isset($option['id'])) {
                        $opt = PollOption::where('id', $option['id'])->first();
                        $opt->update([
                            'option' => $option['option'],
                        ]);
                    } else {
                        $opt = PollOption::create([
                            'polls_id' => $poll->id,
                            'option' => $option['option'],
                        ]);
                    }
                }

                return $this->apiResponse([], 'Poll Updated', true, 200);
            } else {
                $poll = Poll::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'start_date' => date('Y-m-d'),
                    'end_date' => $request->end_date,
                    'created_by' => auth()->user()->id,
                    'status' => $request->status,
                    'is_active' => $request->is_active,
                ]);
                $option_data = json_decode($request->option, true);
                $opt = [];
                foreach ($option_data as $option) {
                    $opt[] = [
                        'polls_id' => $poll->id,
                        'option' => $option,
                    ];
                }
                PollOption::insert($opt);
            }
            return $this->apiResponse([], 'Poll Created', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse(null, $th->getMessage(), false, 500);
        }
    }

    public function pollOptionUpdate(Request $request)
    {
        try {
            $request->validate([
                'option' => 'required',
            ]);
            if ($request->id) {
                $option = PollOption::where('id', $request->id)->first();
                $option->update([
                    'option' => $request->option,
                ]);
                return $this->apiResponse([], 'Poll Option Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse(null, $th->getMessage(), false, 500);
        }
    }


    public function pollDelete($id)
    {
        try {
            Poll::where('id', $id)->delete();
            PollOption::where('polls_id', $id)->delete();
            return $this->apiResponse([], 'Poll Deleted', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse(null, $th->getMessage(), false, 500);
        }
    }

    public function pollOptionDelete(Request $request, $id)
    {
        try {
            $option = PollOption::where('id', $id)->first();
            $option->delete();
            return $this->apiResponse([], 'Poll Option Deleted', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse(null, $th->getMessage(), false, 500);
        }
    }

    public function pollList()
    {
        $polls = Poll::where('created_by', auth()->user()->id)
            ->get();
        foreach ($polls as $poll) {
            $poll->options = PollOption::where('polls_id', $poll->id)
                ->get();
        }
        return $this->apiResponse($polls, 'Poll List', true, 200);
    }

    public function pollDetails($id)
    {
        $poll = Poll::where('id', $id)->first();
        $poll->options = PollOption::where('polls_id', $poll->id)->get();
        return $this->apiResponse($poll, 'Poll Details', true, 200);
    }
}
