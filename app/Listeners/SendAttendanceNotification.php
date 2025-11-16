<?php
namespace App\Listeners;
use App\Events\AttendanceRecorded;
use Illuminate\Support\Facades\Log;

class SendAttendanceNotification {
    public function handle(AttendanceRecorded $event): void {
        Log::info("Attendance recorded for {$event->attendance->student->name}: {$event->attendance->status}");
    }
}