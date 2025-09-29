<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Tkp;

class TkpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tkp::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nmTkp' => $this->faker->word(),
            'kdTkp' => $this->faker->word(),
        ];
    }
}
