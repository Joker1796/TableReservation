<?php

namespace Database\Factories;

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
        'status' => '1',
    ];

    const array UPDATED_ARGUMENTS = [
        'name' => 'test table name updated',
        'description' => 'test description updated',
        'status' => '0',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'status' => $this->faker->randomElement([1, 0]),
        ];
    }
}
