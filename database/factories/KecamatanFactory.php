<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Kecamatan;

class KecamatanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Kecamatan::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'KD_KECAMATAN' => $this->faker->word(),
            'KD_KABUPATEN' => $this->faker->word(),
            'KECAMATAN' => $this->faker->word(),
            'ninput_oleh' => $this->faker->word(),
            'ninput_tgl' => $this->faker->word(),
            'nupdate_oleh' => $this->faker->word(),
            'nupdate_tgl' => $this->faker->word(),
        ];
    }
}
