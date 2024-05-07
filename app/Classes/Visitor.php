<?php

namespace App\Classes;

use Illuminate\Http\Request;
use DB;
use App\Models\VisitorModel;

class Visitor{
    public function jumlah_visit(){
        $jumlah = array();
        $todayCount = DB::table('visitor')
        ->select(DB::raw('IFNULL(SUM(CASE WHEN DATE(date_visit) = CURDATE() THEN 1 ELSE 0 END), "0") AS today_count'))
        ->first()
        ->today_count;

        $thisMonthCount = DB::table('visitor')
            ->select(DB::raw('IFNULL(SUM(CASE WHEN MONTH(date_visit) = MONTH(CURDATE()) AND YEAR(date_visit) = YEAR(CURDATE()) THEN 1 ELSE 0 END), "0") AS this_month_count'))
            ->first()
            ->this_month_count;

        $thisYearCount = DB::table('visitor')
            ->select(DB::raw('IFNULL(SUM(CASE WHEN YEAR(date_visit) = YEAR(CURDATE()) THEN 1 ELSE 0 END), "0") AS this_year_count'))
            ->first()
            ->this_year_count;

        $thisWeekCount = DB::select(DB::raw('
                        SELECT days.day_name, COALESCE(COUNT(data.date_visit), 0) AS total
                        FROM (
                            SELECT "Monday" AS day_name
                            UNION SELECT "Tuesday"
                            UNION SELECT "Wednesday"
                            UNION SELECT "Thursday"
                            UNION SELECT "Friday"
                            UNION SELECT "Saturday"
                            UNION SELECT "Sunday"
                        ) AS days
                        LEFT JOIN visitor AS data ON DAYNAME(data.date_visit) = days.day_name
                        GROUP BY days.day_name
                        ORDER BY FIELD(days.day_name, "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
                    '));

        $jumlah = array(
            'hari_ini' => $todayCount,
            'bulan_ini' => $thisMonthCount,
            'tahun_ini' => $thisYearCount,
            'minggu_ini' => json_encode($thisWeekCount)
        );
        return $jumlah;
        
    }
    public function trigger_visitor(){
        $ip = hash('sha512', request()->ip());
        if(VisitorModel::where('date_visit', today())->where('ip_address', $ip)->count() < 1){
            VisitorModel::create([
                'ip_address' => $ip,
                'date_visit' => today()
            ]);
        }
        return true;
    }
}
