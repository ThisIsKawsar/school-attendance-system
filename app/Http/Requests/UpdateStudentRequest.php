<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'sometimes|required|string|max:255',
            'student_id' => 'sometimes|required|string|unique:students,student_id,' . $this->student->id,
            'class' => 'sometimes|required|string|max:50',
            'section' => 'sometimes|required|string|max:10',
            'photo' => 'nullable|string'
        ];
    }
}