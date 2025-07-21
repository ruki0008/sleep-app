<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sleep = Activity::where('type', '=', 'sleep');
        $exercise = Activity::where('type', '=', 'exercise');
        $s_sum = $sleep->sum('duration');
        $e_sum = $exercise->sum('duration');

        $s_count = Activity::where('type', '=', 'sleep')
        ->select(DB::raw('DATE(start_time) as date'))
        ->distinct()
        ->get()
        ->count();
        $e_count = Activity::where('type', '=', 'exercise')
        ->count();
        $s_avg = round($s_sum / $s_count, 1);
        $e_avg = round($e_sum / $e_count, 1);
        
        return view('activities.index', compact('s_sum', 'e_sum', 's_avg', 'e_avg'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('activities.create');
    }

    public function sleepCreate()
    {
        return view('activities.sleep');
    }

    public function exerciseCreate()
    {
        return view('activities.exercise');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreActivityRequest $request)
    {
        //バリデーション
        $request->validate([
            'type' => 'required',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'quality' => 'required|integer|min:1|max:5',
            'memo' => 'nullable|string|max:255',
        ]);

        //start_timeとend_time,durationを正しい値にする
        $start = Carbon::parse("$request->date $request->start_time");
        $end = Carbon::parse("$request->date $request->end_time");
        $end_time = $end;
        
        $start_h = intval($start->format('H'));
        $end_h = intval($end->format('H'));

        if ($start->gt($end))
        {
            $end_time = $end->copy()->addDay();
        }

        // 重複チェック
        $exists = Activity::where('user_id', Auth::id())
        ->where(function ($query) use ($start, $end_time) {
            $query->whereBetween('start_time', [$start, $end_time])
            ->orWhereBetween('end_time', [$start, $end_time])
            ->orWhere(function ($query2) use ($start, $end_time) {
                $query2->where('start_time', '<=', $start)
                ->where('end_time', '>=', $end_time);
            });
        })
        ->exists();

        if ($exists) {
            return back()
            ->withErrors(['time' => '指定した時間はすでに他の記録と重複しています。'])
            ->withInput();
        }
        
        $activities = [];
        //11時でグラフが切れるため、11時を挟んでいたらデータを分ける
        // $start_h = intval($start->format('H'));
        // $end_h = intval($end->format('H'));
        if($start_h < $end_h){
            if($start_h < 11 & 11 < $end_h){
                //11時で区切ると表示ができないため
                $center = $start->copy()->setTime(11, 00);
                // $center_s = $center_e->copy()->addDay();
                $duration1 = $this->duration($start, $center);
                $activities[] = [
                    'start_time' => $start,
                    'end_time' => $center,
                    'duration' => $duration1,
                ];
                $duration2 = $this->duration($center, $end);
                $activities[] = [
                    'start_time' => $center,
                    'end_time' => $end_time,
                    'duration' => $duration2,
                ];
            }else{
                $duration3 = $this->duration($start, $end);
                $activities[] = [
                    'start_time' => $start,
                    'end_time' => $end_time,
                    'duration' => $duration3,
                ];
            }
        }elseif($start_h > $end_h){
            if($end_h > 11){
                $center_e = $start->copy()->setTime(11, 00);
                $center_s = $center_e->copy()->addDay();
                $duration1 = $this->duration($start, $center_e);
                $activities[] = [
                    'start_time' => $start,
                    'end_time' => $center_e,
                    'duration' => $duration1,
                ];
                $duration2 = $this->duration($center_s, $end_time);
                $activities[] = [
                    'start_time' => $center_s,
                    'end_time' => $end_time,
                    'duration' => $duration2,
                ];
            }else{
                $duration3 = $this->duration($start, $end);
                $activities[] = [
                    'start_time' => $start,
                    'end_time' => $end_time,
                    'duration' => $duration3,
                ];
            }
        }else {
            // start_h == end_h のとき（例：14:00〜14:30）
            $duration3 = $this->duration($start, $end);
            $activities[] = [
                'start_time' => $start,
                'end_time' => $end_time,
                'duration' => $duration3,
            ];
        }
        
        foreach ($activities as $data) {
            $activity = Activity::create([
                'user_id' => Auth::id(),
                'type' => $request->type,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'duration' => $data['duration'],
                'quality' => $request->quality,
                'memo' => $request->memo
            ]);
        }
        
        $request->session()->flash('message', '保存しました');
        return to_route('activities.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        $date = Carbon::parse($activity->start_time)->format('Y-m-d');

        if($activity->type === 'sleep'){
            $file = 'activities.sleepShow';
        }else{
            $file = 'activities.exerciseShow';
        }

        return view($file, compact('activity', 'date'));
    }

    // public function sleepshow(Activity $activity)
    // {
    //     return view('activities.sleepShow', compact('activity'));
    // }

    // public function exerciseShow(Activity $activity)
    // {
    //     return view('activities.exerciseShow', compact('activity'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        // $date = Carbon::parse($activity->start_time)->format('Y-m-d');

        // if($activity->type === 'sleep'){
        //     $file = 'activities.sleepEdit';
        // }else{
        //     $file = 'activities.exerciseEdit';
        // }

        // return view($file, compact('activity', 'date'));
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
    public function destroy(Request $request,Activity $activity)
    {
        $activity->delete();

        $request->session()->flash('message', '削除しました');
        return to_route('analysis.timeGraph');
    }

    private function duration(Carbon $start, Carbon $end): string
    {
        $diff = $start->diff($end);
        $minutes = $diff->i;
        $hours = round(($diff->d * 24) + $diff->h + ($minutes / 60), 1);

        return $hours;
    }
}
