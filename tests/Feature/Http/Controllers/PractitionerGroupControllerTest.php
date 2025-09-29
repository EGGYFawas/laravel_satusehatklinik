<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\PractitionerGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PractitionerGroupController
 */
final class PractitionerGroupControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $practitionerGroups = PractitionerGroup::factory()->count(3)->create();

        $response = $this->get(route('practitioner-groups.index'));

        $response->assertOk();
        $response->assertViewIs('practitionerGroup.index');
        $response->assertViewHas('practitionerGroups');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PractitionerGroupController::class,
            'store',
            \App\Http\Requests\PractitionerGroupStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $namaGroup = $this->faker->word();

        $response = $this->post(route('practitioner-groups.store'), [
            'namaGroup' => $namaGroup,
        ]);

        $practitionerGroups = PractitionerGroup::query()
            ->where('namaGroup', $namaGroup)
            ->get();
        $this->assertCount(1, $practitionerGroups);
        $practitionerGroup = $practitionerGroups->first();

        $response->assertRedirect(route('practitionerGroup.index'));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PractitionerGroupController::class,
            'update',
            \App\Http\Requests\PractitionerGroupUpdateRequest::class
        );
    }

    #[Test]
    public function update_saves_and_redirects(): void
    {
        $practitionerGroup = PractitionerGroup::factory()->create();
        $namaGroup = $this->faker->word();

        $response = $this->put(route('practitioner-groups.update', $practitionerGroup), [
            'namaGroup' => $namaGroup,
        ]);

        $practitionerGroups = PractitionerGroup::query()
            ->where('namaGroup', $namaGroup)
            ->get();
        $this->assertCount(1, $practitionerGroups);
        $practitionerGroup = $practitionerGroups->first();

        $response->assertRedirect(route('practitionerGroup.index'));
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $practitionerGroup = PractitionerGroup::factory()->create();

        $response = $this->delete(route('practitioner-groups.destroy', $practitionerGroup));

        $response->assertRedirect(route('practitionerGroup.index'));

        $this->assertModelMissing($practitionerGroup);
    }
}
