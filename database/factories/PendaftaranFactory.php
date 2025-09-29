<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\Practitioner;
use App\Models\Tkp;

class PendaftaranFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pendaftaran::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'tglDaftar' => $this->faker->date(),
            'keluhan' => $this->faker->word(),
            'kunjSakit' => $this->faker->word(),
            'sistole' => $this->faker->randomFloat(0, 0, 9999999999.),
            'diastole' => $this->faker->randomFloat(0, 0, 9999999999.),
            'beratBadan' => $this->faker->randomFloat(0, 0, 9999999999.),
            'tinggiBadan' => $this->faker->randomFloat(0, 0, 9999999999.),
            'respRate' => $this->faker->randomFloat(0, 0, 9999999999.),
            'lingkarPerut' => $this->faker->randomFloat(0, 0, 9999999999.),
            'heartRate' => $this->faker->randomFloat(0, 0, 9999999999.),
            'rujukBalik' => $this->faker->numberBetween(-10000, 10000),
            'created_by' => $this->faker->numberBetween(-10000, 10000),
            'updated_by' => $this->faker->numberBetween(-10000, 10000),
            'deleted_at' => $this->faker->dateTime(),
            'deleted_by' => $this->faker->numberBetween(-10000, 10000),
            'pasien_id' => Pasien::factory(),
            'poli_id' => Poli::factory(),
            'practitioner_id' => Practitioner::factory(),
            'tkp_id' => Tkp::factory(),
        ];
    }
}
