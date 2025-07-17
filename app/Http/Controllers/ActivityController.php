<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('activities.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('activities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {
        //バリデーション
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'quality' => 'required|integer|min:1|max:5',
            'memo' => 'nullable|string|max:255',
        ]);

        //start_timeとend_time,durationを正しい値にする
        $start = Carbon::parse("$request->date $request->start_time");
        $end = Carbon::parse("$request->date $request->end_time");
        if ($start->gt($end))
        {
            $end->addDay();
        }
        $diff = $start->diff($end);
        $hours = $diff->h + $diff->d * 24;
        $minutes = $diff->i;

        $duration = "{$hours}時間{$minutes}分";
        // dd($start, $end, $duration);
        
        $activity = Activity::create([
            'user_id' => Auth::id(),
            'start_time' => $start,
            'end_time' => $end,
            'duration' => $duration,
            'quality' => $request->quality,
            'memo' => $request->memo
        ]);
        
        $request->session()->flash('message', '保存しました');
        return to_route('activities.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        //
    }
}
