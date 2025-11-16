<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student_name' => $this->student->name,
            'student_class' => $this->student->class,
            'student_section' => $this->student->section,
            'date' => $this->date->format('Y-m-d'),
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at,
        ];
    }
}