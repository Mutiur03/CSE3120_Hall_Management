<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'roll' => fake()->unique()->numerify('20######'),
            'registration_no' => fake()->unique()->numerify('REG-####'),
            'department' => fake()->randomElement(['CSE', 'EEE', 'BBA', 'English']),
            'academic_session' => '2024-2025',
            'phone' => fake()->numerify('01#########'),
            'status' => StudentStatus::Active,
        ];
    }
}
