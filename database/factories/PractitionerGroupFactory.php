<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\PractitionerGroup;

class PractitionerGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PractitionerGroup::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'namaGroup' => $this->faker->word(),
        ];
    }
}
