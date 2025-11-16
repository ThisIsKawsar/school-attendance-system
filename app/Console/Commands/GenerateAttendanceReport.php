<?php
namespace App\Console\Commands;
use App\Services\AttendanceService;
use Illuminate\Console\Command;

class GenerateAttendanceReport extends Command {
    protected $signature = 'attendance:generate-report {month} {class?}';
    protected $description = 'Generate monthly attendance report';

    public function handle(AttendanceService $attendanceService): int {
        $month = $this->argument('month');
        $class = $this->argument('class');

        $this->info("Generating attendance report for {$month}" . ($class ? " (Class: {$class})" : ''));

        $report = $attendanceService->getMonthlyReport($month, $class);
        $filename = "attendance_report_{$month}" . ($class ? "_{$class}" : '') . ".csv";
        $filepath = storage_path("app/reports/{$filename}");

        if (!is_dir(dirname($filepath))) mkdir(dirname($filepath), 0755, true);

        $handle = fopen($filepath, 'w');
        fputcsv($handle, ['Student Name', 'Student ID', 'Class', 'Section', 'Present Days', 'Total Days', 'Percentage']);

        foreach ($report as $record) {
            fputcsv($handle, [
                $record['student']->name,
                $record['student']->student_id,
                $record['student']->class,
                $record['student']->section,
                $record['present_days'],
                $record['total_days'],
                $record['percentage'] . '%'
            ]);
        }

        fclose($handle);
        $this->info("Report generated: {$filepath}");
        $this->info("Total students: " . count($report));

        return Command::SUCCESS;
    }
}