<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_students(): void
    {
        Student::factory()->count(3)->create();

        $response = $this->getJson('/api/students');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id', 'name', 'student_id', 'class', 'section',
                             'attendance_percentage', 'current_status'
                         ]
                     ]
                 ]);
    }

    public function test_can_create_student(): void
    {
        $studentData = [
            'name' => 'John Doe',
            'student_id' => 'STU1001',
            'class' => '10',
            'section' => 'A'
        ];

        $response = $this->postJson('/api/students', $studentData);

        $response->assertStatus(201)
                 ->assertJsonFragment([ // Fix: Use assertJsonFragment instead
                     'name' => 'John Doe',
                     'student_id' => 'STU1001'
                 ]);

        $this->assertDatabaseHas('students', $studentData);
    }

    public function test_can_update_student(): void
    {
        $student = Student::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'class' => '11'
        ];

        $response = $this->putJson("/api/students/{$student->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment([ // Fix: Use assertJsonFragment
                     'name' => 'Updated Name',
                     'class' => '11'
                 ]);
    }

    public function test_can_delete_student(): void
    {
        $student = Student::factory()->create();

        $response = $this->deleteJson("/api/students/{$student->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Student deleted successfully']);

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }
}