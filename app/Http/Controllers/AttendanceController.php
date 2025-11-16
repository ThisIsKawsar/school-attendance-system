<?php

namespace App\Http\Controllers;

use App\Http\Resources\AttendanceResource;
use App\Http\Requests\BulkAttendanceRequest;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService) {}

    public function bulkStore(BulkAttendanceRequest $request)
    {
        $attendance = $this->attendanceService->recordBulkAttendance($request->attendance_data);
        return AttendanceResource::collection($attendance);
    }

    public function monthlyReport(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'class' => 'nullable|string'
        ]);
        
        $report = $this->attendanceService->getMonthlyReport($request->month, $request->class);
        return response()->json($report);
    }

    public function todayAttendance(Request $request): JsonResponse
    {
        $query = \App\Models\Attendance::with('student')->whereDate('date', today());
        
        if ($request->class) {
            $query->whereHas('student', fn($q) => $q->where('class', $request->class));
        }
        if ($request->section) {
            $query->whereHas('student', fn($q) => $q->where('section', $request->section));
        }

        return response()->json($query->get());
    }
}