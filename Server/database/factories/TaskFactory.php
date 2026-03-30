<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * TaskFactory
 * Factory for generating fake task data in tests and seeding
 * Follows business rules for valid task generation
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state
     *
     * @return array Default task attributes
     */
    public function definition(): array
    {
        return [
            # Random task title
            'title' => fake()->sentence(3),

            # Random future or today due date
            'due_date' => fake()->dateTimeBetween('today', '+30 days')->format('Y-m-d'),

            # Random priority
            'priority' => fake()->randomElement(['low', 'medium', 'high']),

            # Default status is pending per business rules
            'status' => 'pending',
        ];
    }

    /**
     * State for high priority task
     *
     * @return static
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * State for medium priority task
     *
     * @return static
     */
    public function mediumPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'medium',
        ]);
    }

    /**
     * State for low priority task
     *
     * @return static
     */
    public function lowPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'low',
        ]);
    }

    /**
     * State for pending status
     *
     * @return static
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * State for in_progress status
     *
     * @return static
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    /**
     * State for done status
     *
     * @return static
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'done',
        ]);
    }
}