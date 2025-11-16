<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $attendanceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendanceService = app(AttendanceService::class);
    }

    public function test_bulk_attendance_recording(): void
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $attendanceData = [
            [
                'student_id' => $student1->id,
                'status' => 'present',
                'note' => 'On time'
            ],
            [
                'student_id' => $student2->id,
                'status' => 'absent',
                'note' => 'Sick'
            ]
        ];

        $result = $this->attendanceService->recordBulkAttendance($attendanceData);

        $this->assertCount(2, $result);
        $this->assertEquals('present', $result[0]->status);
        $this->assertEquals('absent', $result[1]->status);
    }

    public function test_monthly_report_generation(): void
    {
        $student = Student::factory()->create(['class' => '10']);
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => now()->startOfMonth(),
            'status' => 'present'
        ]);

        $report = $this->attendanceService->getMonthlyReport(now()->format('Y-m'), '10');

        $this->assertCount(1, $report);
        $this->assertEquals(100, $report[0]['percentage']);
    }

    public function test_attendance_statistics_caching(): void
    {
        $student = Student::factory()->create();
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => now(),
            'status' => 'present'
        ]);

        // First call should cache the result
        $stats1 = $this->attendanceService->getAttendanceStatistics();
        
        // Second call should use cache
        $stats2 = $this->attendanceService->getAttendanceStatistics();

        $this->assertEquals($stats1, $stats2);
        $this->assertArrayHasKey('today', $stats1);
        $this->assertArrayHasKey('month', $stats1);
    }
}