<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'date',
        'breakfast',
        'lunch',
        'dinner',
        'meal_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'breakfast' => 'boolean',
            'lunch' => 'boolean',
            'dinner' => 'boolean',
            'meal_active' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('meal_active', true);
    }
}
