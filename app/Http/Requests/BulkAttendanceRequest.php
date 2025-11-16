<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class BulkAttendanceRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'attendance_data' => 'required|array',
            'attendance_data.*.student_id' => 'required|exists:students,id',
            'attendance_data.*.status' => 'required|in:present,absent,late',
            'attendance_data.*.note' => 'nullable|string|max:500'
        ];
    }
}