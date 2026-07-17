<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiningAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'meal_type',
        'present',
        'time',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'present' => 'boolean',
            'time' => 'datetime:H:i',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeToday($query)
    {
        return $query->where('date', today());
    }

    public function scopeMealType($query, $type)
    {
        return $query->where('meal_type', $type);
    }

    public function scopePresent($query)
    {
        return $query->where('present', true);
    }
}
