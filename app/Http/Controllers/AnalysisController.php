<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Activity;

class AnalysisController extends Controller
{
    public function timeGraph()
    {
        $activities = Activity::orderBy('start_time')->get();
        
        $graph_datas = [];
        $graph_data = [];

        foreach ($activities as $activity) {
            $start = Carbon::parse($activity->start_time);
            $end = Carbon::parse($activity->end_time);

            $dateKey = $start->format('Y-m-d'); //表示上の縦軸（日）
            $start_t = $start->format('H:i'); //11:0
            $end_t = $end->format('H:i'); //9:15

            // 横軸は0〜24時 → decimal時間に変換（23:30 → 23.5）
            // $startHour = $start->hour + $start->minute / 60;
            // $endHour = $end->hour + $end->minute / 60;
            [$s_hour, $s_minute] = explode(':', $start_t);
            $start_hours = $s_hour + round(($s_minute / 60), 1);
            [$e_hour, $e_minute] = explode(':', $end_t);
            $end_hours = $e_hour + round(($e_minute / 60), 1);

            // 翌日になった場合（例：23:00 → 06:00）
            if ($end < $start) {
                $end_hours += 24;
            }

            $graph_data[] = [
                'date' => $dateKey,
                'weekday' => $start->isoFormat('dd'), // 月火水…
                'start' => $start_hours,
                'end' => $end_hours
            ];
            // $graphDatas[] = $graph_data;
        }
        return view('analysis.activitiyTime', ['graph_data' => $graph_data]);
    }
}
