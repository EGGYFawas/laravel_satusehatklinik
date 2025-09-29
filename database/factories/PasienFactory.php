<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\KDKELURAHAN;
use App\Models\Kelurahan;
use App\Models\Pasien;

class PasienFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pasien::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'noKartu' => $this->faker->numerify(str_repeat('#', 8)),
            'nama' => $this->faker->name(),
            'sex' => $this->faker->randomElement(['L', 'P']),
            'tglLahir' => $this->faker->date(),
            'jnsPeserta' => $this->faker->numberBetween(1, 2),
            'golDarah' => $this->faker->randomElement(['A', 'B', 'AB', 'O']),
            'noHp' => '+6285' . $this->faker->numerify(str_repeat('#', 9)), 
            'noKtp' => $this->faker->numerify(str_repeat('#', 16)),
            'pstProl' => $this->faker->word(),
            'pstPrb' => $this->faker->word(),
            'aktif' => $this->faker->boolean(),
            'ketAktif' => $this->faker->word(),
            'alamat' => $this->faker->address(),
            'KD_KELURAHAN' => Kelurahan::all()->random()->KD_KELURAHAN,
            'created_by' => null,
            'updated_by' => null,
            'deleted_at' => null,
            'deleted_by' => null,
        ];
    }
}