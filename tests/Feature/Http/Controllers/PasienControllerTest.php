<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PasienController
 */
final class PasienControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $pasiens = Pasien::factory()->count(3)->create();

        $response = $this->get(route('pasiens.index'));

        $response->assertOk();
        $response->assertViewIs('pasien.index');
        $response->assertViewHas('pasiens');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PasienController::class,
            'store',
            \App\Http\Requests\PasienStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $nomorRm = $this->faker->numberBetween(-10000, 10000);
        $noKartu = $this->faker->word();
        $nama = $this->faker->word();
        $sex = $this->faker->randomLetter();
        $tglLahir = Carbon::parse($this->faker->date());
        $jnsPeserta = $this->faker->numberBetween(-10000, 10000);
        $golDarah = $this->faker->word();
        $noHp = $this->faker->word();
        $noKtp = $this->faker->word();
        $pstProl = $this->faker->word();
        $pstPrb = $this->faker->word();
        $aktif = $this->faker->boolean();
        $ketAktif = $this->faker->word();
        $alamat = $this->faker->word();

        $response = $this->post(route('pasiens.store'), [
            'nomorRm' => $nomorRm,
            'noKartu' => $noKartu,
            'nama' => $nama,
            'sex' => $sex,
            'tglLahir' => $tglLahir->toDateString(),
            'jnsPeserta' => $jnsPeserta,
            'golDarah' => $golDarah,
            'noHp' => $noHp,
            'noKtp' => $noKtp,
            'pstProl' => $pstProl,
            'pstPrb' => $pstPrb,
            'aktif' => $aktif,
            'ketAktif' => $ketAktif,
            'alamat' => $alamat,
        ]);

        $pasiens = Pasien::query()
            ->where('nomorRm', $nomorRm)
            ->where('noKartu', $noKartu)
            ->where('nama', $nama)
            ->where('sex', $sex)
            ->where('tglLahir', $tglLahir)
            ->where('jnsPeserta', $jnsPeserta)
            ->where('golDarah', $golDarah)
            ->where('noHp', $noHp)
            ->where('noKtp', $noKtp)
            ->where('pstProl', $pstProl)
            ->where('pstPrb', $pstPrb)
            ->where('aktif', $aktif)
            ->where('ketAktif', $ketAktif)
            ->where('alamat', $alamat)
            ->get();
        $this->assertCount(1, $pasiens);
        $pasien = $pasiens->first();

        $response->assertRedirect(route('pasien.index'));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PasienController::class,
            'update',
            \App\Http\Requests\PasienUpdateRequest::class
        );
    }

    #[Test]
    public function update_saves_and_redirects(): void
    {
        $pasien = Pasien::factory()->create();
        $nomorRm = $this->faker->numberBetween(-10000, 10000);
        $noKartu = $this->faker->word();
        $nama = $this->faker->word();
        $sex = $this->faker->randomLetter();
        $tglLahir = Carbon::parse($this->faker->date());
        $jnsPeserta = $this->faker->numberBetween(-10000, 10000);
        $golDarah = $this->faker->word();
        $noHp = $this->faker->word();
        $noKtp = $this->faker->word();
        $pstProl = $this->faker->word();
        $pstPrb = $this->faker->word();
        $aktif = $this->faker->boolean();
        $ketAktif = $this->faker->word();
        $alamat = $this->faker->word();

        $response = $this->put(route('pasiens.update', $pasien), [
            'nomorRm' => $nomorRm,
            'noKartu' => $noKartu,
            'nama' => $nama,
            'sex' => $sex,
            'tglLahir' => $tglLahir->toDateString(),
            'jnsPeserta' => $jnsPeserta,
            'golDarah' => $golDarah,
            'noHp' => $noHp,
            'noKtp' => $noKtp,
            'pstProl' => $pstProl,
            'pstPrb' => $pstPrb,
            'aktif' => $aktif,
            'ketAktif' => $ketAktif,
            'alamat' => $alamat,
        ]);

        $pasiens = Pasien::query()
            ->where('nomorRm', $nomorRm)
            ->where('noKartu', $noKartu)
            ->where('nama', $nama)
            ->where('sex', $sex)
            ->where('tglLahir', $tglLahir)
            ->where('jnsPeserta', $jnsPeserta)
            ->where('golDarah', $golDarah)
            ->where('noHp', $noHp)
            ->where('noKtp', $noKtp)
            ->where('pstProl', $pstProl)
            ->where('pstPrb', $pstPrb)
            ->where('aktif', $aktif)
            ->where('ketAktif', $ketAktif)
            ->where('alamat', $alamat)
            ->get();
        $this->assertCount(1, $pasiens);
        $pasien = $pasiens->first();

        $response->assertRedirect(route('pasien.index'));
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $pasien = Pasien::factory()->create();

        $response = $this->delete(route('pasiens.destroy', $pasien));

        $response->assertRedirect(route('pasien.index'));

        $this->assertModelMissing($pasien);
    }
}
