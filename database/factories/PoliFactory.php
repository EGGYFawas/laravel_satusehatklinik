<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Poli;

class PoliFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poli::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'kodePoli' => $this->faker->word(),
            'namaPoli' => $this->faker->word(),
        ];
    }
}
