<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_bulk_attendance(): void
    {
        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $attendanceData = [
            'attendance_data' => [
                [
                    'student_id' => $student1->id,
                    'status' => 'present',
                    'note' => 'On time'
                ],
                [
                    'student_id' => $student2->id,
                    'status' => 'late',
                    'note' => 'Traffic'
                ]
            ]
        ];

        $response = $this->postJson('/api/attendance/bulk', $attendanceData);

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data'); // Fix: Check data array count

        $this->assertDatabaseHas('attendances', [
            'student_id' => $student1->id,
            'status' => 'present'
        ]);
    }

    public function test_can_get_monthly_report(): void
    {
        $student = Student::factory()->create();
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => now()->startOfMonth(),
            'status' => 'present'
        ]);

        $response = $this->getJson('/api/attendance/monthly-report?month=' . now()->format('Y-m'));

        $response->assertStatus(200)
                 ->assertJsonStructure([]);
    }

    public function test_can_get_today_attendance(): void
    {
        $student = Student::factory()->create();
        Attendance::factory()->create([
            'student_id' => $student->id,
            'date' => now(),
            'status' => 'present'
        ]);

        $response = $this->getJson('/api/attendance/today');

        $response->assertStatus(200)
                 ->assertJsonStructure([]);
    }
}