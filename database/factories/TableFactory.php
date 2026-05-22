<?php

namespace Database\Factories;

use App\Enums\TableStatus;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    const array ARGUMENTS = [
        'name' => 'test table name',
        'description' => 'test description',
        'status' => TableStatus::READY->value,
    ];

    const array UPDATED_ARGUMENTS = [
        'name' => 'test table name updated',
        'description' => 'test description updated',
        'status' => TableStatus::NOT_READY->value,
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement(TableStatus::cases()),
        ];
    }
}
