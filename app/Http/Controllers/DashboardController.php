<?php

namespace App\Http\Controllers;

use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    public function index()
    {
        return view('dashboard');
    }

    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->attendanceService->getAttendanceStatistics();
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json([
                'today' => ['present' => 0, 'absent' => 0, 'late' => 0],
                'month' => ['present' => 0, 'absent' => 0, 'late' => 0],
                'total_students' => 0
            ]);
        }
    }

    public function todayAttendance(): JsonResponse
    {
        try {
            $attendance = \App\Models\Attendance::with('student')
                ->whereDate('date', today())
                ->get();
            return response()->json($attendance);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}