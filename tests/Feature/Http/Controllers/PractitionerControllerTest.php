<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Practitioner;
use App\Models\PractitionerGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PractitionerController
 */
final class PractitionerControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $practitioners = Practitioner::factory()->count(3)->create();

        $response = $this->get(route('practitioners.index'));

        $response->assertOk();
        $response->assertViewIs('practitioner.index');
        $response->assertViewHas('practitioners');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PractitionerController::class,
            'store',
            \App\Http\Requests\PractitionerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $namaPractitioner = $this->faker->word();
        $nikPractitioner = $this->faker->word();
        $practitioner_group = PractitionerGroup::factory()->create();

        $response = $this->post(route('practitioners.store'), [
            'namaPractitioner' => $namaPractitioner,
            'nikPractitioner' => $nikPractitioner,
            'practitioner_group_id' => $practitioner_group->id,
        ]);

        $practitioners = Practitioner::query()
            ->where('namaPractitioner', $namaPractitioner)
            ->where('nikPractitioner', $nikPractitioner)
            ->where('practitioner_group_id', $practitioner_group->id)
            ->get();
        $this->assertCount(1, $practitioners);
        $practitioner = $practitioners->first();

        $response->assertRedirect(route('practitioner.index'));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PractitionerController::class,
            'update',
            \App\Http\Requests\PractitionerUpdateRequest::class
        );
    }

    #[Test]
    public function update_saves_and_redirects(): void
    {
        $practitioner = Practitioner::factory()->create();
        $namaPractitioner = $this->faker->word();
        $nikPractitioner = $this->faker->word();
        $practitioner_group = PractitionerGroup::factory()->create();

        $response = $this->put(route('practitioners.update', $practitioner), [
            'namaPractitioner' => $namaPractitioner,
            'nikPractitioner' => $nikPractitioner,
            'practitioner_group_id' => $practitioner_group->id,
        ]);

        $practitioners = Practitioner::query()
            ->where('namaPractitioner', $namaPractitioner)
            ->where('nikPractitioner', $nikPractitioner)
            ->where('practitioner_group_id', $practitioner_group->id)
            ->get();
        $this->assertCount(1, $practitioners);
        $practitioner = $practitioners->first();

        $response->assertRedirect(route('practitioner.index'));
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $practitioner = Practitioner::factory()->create();

        $response = $this->delete(route('practitioners.destroy', $practitioner));

        $response->assertRedirect(route('practitioner.index'));

        $this->assertModelMissing($practitioner);
    }
}
