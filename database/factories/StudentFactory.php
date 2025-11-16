<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory {
    public function definition(): array {
        return [
            'name' => fake()->name(),
            'student_id' => 'STU' . fake()->unique()->numberBetween(1000, 9999),
            'class' => fake()->randomElement(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10']),
            'section' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'photo' => fake()->optional()->imageUrl(100, 100, 'people'),
        ];
    }
}