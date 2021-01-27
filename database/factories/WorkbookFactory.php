<?php

namespace Database\Factories;

use App\Models\Workbook;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkbookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Workbook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
