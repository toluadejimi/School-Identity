<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_number' => 'STU-'.fake()->unique()->numerify('####'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'class_name' => fake()->randomElement(['JSS1A', 'JSS2B', 'SS1A', 'SS2A']),
            'department' => fake()->randomElement(['Science', 'Arts', 'Commercial']),
            'status' => 'active',
        ];
    }
}
