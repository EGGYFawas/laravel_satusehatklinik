<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\Practitioner;
use App\Models\Tkp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PendaftaranController
 */
final class PendaftaranControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $pendaftarans = Pendaftaran::factory()->count(3)->create();

        $response = $this->get(route('pendaftarans.index'));

        $response->assertOk();
        $response->assertViewIs('pendaftaran.index');
        $response->assertViewHas('pendaftarans');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PendaftaranController::class,
            'store',
            \App\Http\Requests\PendaftaranStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $tglDaftar = Carbon::parse($this->faker->date());
        $pasien = Pasien::factory()->create();
        $poli = Poli::factory()->create();
        $practitioner = Practitioner::factory()->create();
        $tkp = Tkp::factory()->create();

        $response = $this->post(route('pendaftarans.store'), [
            'tglDaftar' => $tglDaftar->toDateString(),
            'pasien_id' => $pasien->id,
            'poli_id' => $poli->id,
            'practitioner_id' => $practitioner->id,
            'tkp_id' => $tkp->id,
        ]);

        $pendaftarans = Pendaftaran::query()
            ->where('tglDaftar', $tglDaftar)
            ->where('pasien_id', $pasien->id)
            ->where('poli_id', $poli->id)
            ->where('practitioner_id', $practitioner->id)
            ->where('tkp_id', $tkp->id)
            ->get();
        $this->assertCount(1, $pendaftarans);
        $pendaftaran = $pendaftarans->first();

        $response->assertRedirect(route('pendaftaran.index'));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PendaftaranController::class,
            'update',
            \App\Http\Requests\PendaftaranUpdateRequest::class
        );
    }

    #[Test]
    public function update_saves_and_redirects(): void
    {
        $pendaftaran = Pendaftaran::factory()->create();
        $tglDaftar = Carbon::parse($this->faker->date());
        $pasien = Pasien::factory()->create();
        $poli = Poli::factory()->create();
        $practitioner = Practitioner::factory()->create();
        $tkp = Tkp::factory()->create();

        $response = $this->put(route('pendaftarans.update', $pendaftaran), [
            'tglDaftar' => $tglDaftar->toDateString(),
            'pasien_id' => $pasien->id,
            'poli_id' => $poli->id,
            'practitioner_id' => $practitioner->id,
            'tkp_id' => $tkp->id,
        ]);

        $pendaftarans = Pendaftaran::query()
            ->where('tglDaftar', $tglDaftar)
            ->where('pasien_id', $pasien->id)
            ->where('poli_id', $poli->id)
            ->where('practitioner_id', $practitioner->id)
            ->where('tkp_id', $tkp->id)
            ->get();
        $this->assertCount(1, $pendaftarans);
        $pendaftaran = $pendaftarans->first();

        $response->assertRedirect(route('pendaftaran.index'));
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $pendaftaran = Pendaftaran::factory()->create();

        $response = $this->delete(route('pendaftarans.destroy', $pendaftaran));

        $response->assertRedirect(route('pendaftaran.index'));

        $this->assertModelMissing($pendaftaran);
    }
}
