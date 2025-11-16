<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'name' => 'required|string|max:255',
            'student_id' => 'required|string|unique:students,student_id',
            'class' => 'required|string|max:50',
            'section' => 'required|string|max:10',
            'photo' => 'nullable|string'
        ];
    }
}