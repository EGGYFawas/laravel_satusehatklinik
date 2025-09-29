<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Provinsi;

class ProvinsiFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Provinsi::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'KD_PROVINSI' => $this->faker->word(),
            'PROVINSI' => $this->faker->word(),
            'ninput_oleh' => $this->faker->word(),
            'ninput_tgl' => $this->faker->word(),
            'nupdate_oleh' => $this->faker->word(),
            'nupdate_tgl' => $this->faker->word(),
        ];
    }
}
