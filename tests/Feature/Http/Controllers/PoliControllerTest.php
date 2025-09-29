<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Poli;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PoliController
 */
final class PoliControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $polis = Poli::factory()->count(3)->create();

        $response = $this->get(route('polis.index'));

        $response->assertOk();
        $response->assertViewIs('poli.index');
        $response->assertViewHas('polis');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PoliController::class,
            'store',
            \App\Http\Requests\PoliStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $kodePoli = $this->faker->word();
        $namaPoli = $this->faker->word();

        $response = $this->post(route('polis.store'), [
            'kodePoli' => $kodePoli,
            'namaPoli' => $namaPoli,
        ]);

        $polis = Poli::query()
            ->where('kodePoli', $kodePoli)
            ->where('namaPoli', $namaPoli)
            ->get();
        $this->assertCount(1, $polis);
        $poli = $polis->first();

        $response->assertRedirect(route('poli.index'));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PoliController::class,
            'update',
            \App\Http\Requests\PoliUpdateRequest::class
        );
    }

    #[Test]
    public function update_saves_and_redirects(): void
    {
        $poli = Poli::factory()->create();
        $kodePoli = $this->faker->word();
        $namaPoli = $this->faker->word();

        $response = $this->put(route('polis.update', $poli), [
            'kodePoli' => $kodePoli,
            'namaPoli' => $namaPoli,
        ]);

        $polis = Poli::query()
            ->where('kodePoli', $kodePoli)
            ->where('namaPoli', $namaPoli)
            ->get();
        $this->assertCount(1, $polis);
        $poli = $polis->first();

        $response->assertRedirect(route('poli.index'));
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $poli = Poli::factory()->create();

        $response = $this->delete(route('polis.destroy', $poli));

        $response->assertRedirect(route('poli.index'));

        $this->assertModelMissing($poli);
    }
}
