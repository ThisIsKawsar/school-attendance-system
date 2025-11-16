<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First, clear existing data
        DB::table('attendances')->delete();
        DB::table('students')->delete();
        DB::table('users')->delete();

        // Create admin user
        User::create([
            'name' => 'School Admin',
            'email' => 'admin@school.com',
            'password' => Hash::make('password'),
        ]);

        // Create students
        $students = [
            ['name' => 'John Smith', 'student_id' => 'STU1001', 'class' => '10', 'section' => 'A', 'photo' => null],
            ['name' => 'Emma Johnson', 'student_id' => 'STU1002', 'class' => '10', 'section' => 'A', 'photo' => null],
            ['name' => 'Michael Brown', 'student_id' => 'STU1003', 'class' => '10', 'section' => 'A', 'photo' => null],
            ['name' => 'Sarah Davis', 'student_id' => 'STU1004', 'class' => '10', 'section' => 'B', 'photo' => null],
            ['name' => 'David Wilson', 'student_id' => 'STU1005', 'class' => '10', 'section' => 'B', 'photo' => null],
            ['name' => 'Lisa Miller', 'student_id' => 'STU1006', 'class' => '9', 'section' => 'A', 'photo' => null],
            ['name' => 'James Taylor', 'student_id' => 'STU1007', 'class' => '9', 'section' => 'A', 'photo' => null],
            ['name' => 'Jennifer Lee', 'student_id' => 'STU1008', 'class' => '9', 'section' => 'B', 'photo' => null],
            ['name' => 'Robert Clark', 'student_id' => 'STU1009', 'class' => '8', 'section' => 'A', 'photo' => null],
            ['name' => 'Maria Garcia', 'student_id' => 'STU1010', 'class' => '8', 'section' => 'A', 'photo' => null],
        ];

        foreach ($students as $student) {
            DB::table('students')->insert([
                'name' => $student['name'],
                'student_id' => $student['student_id'],
                'class' => $student['class'],
                'section' => $student['section'],
                'photo' => $student['photo'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create sample attendance for today
        $studentIds = DB::table('students')->pluck('id');
        $today = now()->format('Y-m-d');

        foreach ($studentIds as $studentId) {
            DB::table('attendances')->insert([
                'student_id' => $studentId,
                'date' => $today,
                'status' => $this->getRandomStatus(),
                'note' => $this->getRandomNote(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getRandomStatus(): string
    {
        $statuses = ['present', 'present', 'present', 'present', 'absent', 'late'];
        return $statuses[array_rand($statuses)];
    }

    private function getRandomNote(): ?string
    {
        $notes = [null, 'Sick leave', 'Personal reason', 'Doctor appointment', 'Family event'];
        return $notes[array_rand($notes)];
    }
}