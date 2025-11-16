<?php
namespace App\Services;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AttendanceService {
    public function recordBulkAttendance(array $attendanceData): Collection {
        $results = collect();
        $today = now()->toDateString();

        DB::transaction(function () use ($attendanceData, $today, $results) {
            foreach ($attendanceData as $data) {
                $attendance = Attendance::updateOrCreate(
                    ['student_id' => $data['student_id'], 'date' => $today],
                    ['status' => $data['status'], 'note' => $data['note'] ?? null]
                );
                $results->push($attendance);
            }
        });

        $this->clearAttendanceCache();
        return $results;
    }

    public function getMonthlyReport(string $month, ?string $class = null): Collection {
        $cacheKey = "attendance_report_{$month}_" . ($class ?? 'all');
        
        return Cache::remember($cacheKey, 3600, function () use ($month, $class) {
            $query = Student::with(['attendances' => function ($query) use ($month) {
                $query->whereMonth('date', date('m', strtotime($month)))
                      ->whereYear('date', date('Y', strtotime($month)));
            }]);

            if ($class) $query->where('class', $class);

            return $query->get()->map(function ($student) {
                $presentDays = $student->attendances->where('status', 'present')->count();
                $totalDays = $student->attendances->count();
                $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

                return [
                    'student' => $student,
                    'present_days' => $presentDays,
                    'total_days' => $totalDays,
                    'percentage' => $percentage
                ];
            });
        });
    }

    public function getAttendanceStatistics(): array {
        return Cache::remember('attendance_statistics', 300, function () {
            $today = now()->toDateString();
            
            $todayStats = Attendance::where('date', $today)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $monthStats = Attendance::whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            return [
                'today' => $todayStats,
                'month' => $monthStats,
                'total_students' => Student::count()
            ];
        });
    }

    private function clearAttendanceCache(): void {
        Cache::forget('attendance_statistics');
    }
}