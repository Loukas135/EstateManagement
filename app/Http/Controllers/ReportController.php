<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estate;
use App\Models\User;
use App\Models\Category;
use App\Models\Work;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // General report of estates
    public function estateReport()
    {
        $estates = Estate::with(['category', 'user'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $estates
        ]);
    }

    // General report of users
    public function userReport()
    {
        $users = User::withCount(['estates', 'works'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    // Report on works
    public function workReport()
    {
        $works = Work::with(['extra'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $works
        ]);
    }

    // Custom report for filtering data based on a time range
    public function customReport(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $reports = DB::table('estates')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $reports
        ]);
    }
}
