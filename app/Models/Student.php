<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'student_id', 'class', 'section', 'photo'];

    protected $appends = ['attendance_percentage', 'current_status'];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    protected function attendancePercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                try {
                    $total = $this->attendances()->count();
                    if ($total === 0) return 0;
                    
                    $present = $this->attendances()->where('status', 'present')->count();
                    return round(($present / $total) * 100, 2);
                } catch (\Exception $e) {
                    return 0;
                }
            }
        );
    }

    protected function currentStatus(): Attribute
    {
        return Attribute::make(
            get: function () {
                try {
                    return $this->attendances()->whereDate('date', today())->value('status') ?? 'absent';
                } catch (\Exception $e) {
                    return 'absent';
                }
            }
        );
    }
}