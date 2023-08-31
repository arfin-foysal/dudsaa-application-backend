<?php

namespace App\Http\Controllers;

use App\Http\Traits\HelperTrait;
use App\Models\Notice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NoticeController extends Controller
{
    use HelperTrait;
    public function noticeList(Request $request)
    {
        $month = $request->query('month');
        $year = $request->query('year');

        $notice = Notice::leftJoin('users', 'notices.published_by', 'users.id')
            ->select(
                'notices.id',
                'notices.published_by',
                'notices.title',
                'notices.body',
                'notices.is_active',
                'notices.created_at',
                'users.name as publisher_name'
            )
            ->when($month, function ($query, $month) {
                return $query->whereMonth('notices.created_at', $month);
            })
            ->when($year, function ($query, $year) {
                return $query->whereYear('notices.created_at', $year);
            })
            ->latest()
            ->get();
        return $this->apiResponse($notice, 'Notice List', true, 200);
    }

    public function noticeDetails($id)
    {
        $notice = Notice::leftJoin('users', 'notices.published_by', 'users.id')
            ->select(
                'notices.*',
                'users.name as publisher_name'
            )
            ->where('notices.id', $id)
            ->latest()
            ->first();
        return $this->apiResponse($notice, 'Notice Details', true, 200);
    }

    public function noticeSaveOrUpdate(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'body' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return $this->apiResponse($validateUser->errors(), 'validation error', false, 422);
            }

            $notice = [
                'title' => $request->title,
                'body' => $request->body,
                'published_by' => Auth::user()->id,
                'is_active' => true,
                'created_at' => Carbon::now(),
            ];

            if (empty($request->id)) {
                Notice::insert($notice);
                return $this->apiResponse([], 'Notice Created', true, 200);
            } else {
                Notice::where('id', $request->id)->update($notice);
                return $this->apiResponse([], 'Notice Updated', true, 200);
            }
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }

    public function noticeDelete($id)
    {
        try {
            Notice::where('id', $id)->delete();
            return $this->apiResponse([], 'Notice Deleted', true, 200);
        } catch (\Throwable $th) {
            return $this->apiResponse([], $th->getMessage(), false, 500);
        }
    }
}
