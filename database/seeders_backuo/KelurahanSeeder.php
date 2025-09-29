<?php

namespace Database\Seeders;

use App\Models\Kelurahan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelurahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kelurahanData =[
            ['KD_KELURAHAN' => '3517160000', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'KESAMBEN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160001', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'KEDUNG BETIK',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160002', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'KEDUNG MLATI',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160003', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'WATU DAKON',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160004', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'CARANG REJO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160005', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'JOMBOK',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160006', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'BLIMBING',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160007', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'WULUH',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160008', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'POJOK REJO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160009', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'KESAMBEN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160010', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'PODOROTO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160011', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'JOMBATAN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160012', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'JATI DUWUR',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160013', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'POJOK KULON',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517160014', 'KD_KECAMATAN' => '3517160', 'KELURAHAN' => 'GUMULAN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100001', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'PLOSOKEREP',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100002', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'JOGOLOYO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100003', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'PALREJO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100004', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'PLEMAHAN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100005', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'BRUDU',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100006', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'BADAS',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100007', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'NGLELE',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100008', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'TRAWASAN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100009', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'SEBANI',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100010', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'MLARAS',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100011', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'SEGODOREJO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100012', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'KEDUNGPAPAR',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100013', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'SUMOBITO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100014', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'CURAH MALANG',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100015', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'BUDUGSIDOREJO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100016', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'KENDALSARI',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100017', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'TALUN KIDUL',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100018', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'MADYO PURO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100019', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'BAKALAN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100020', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'GEDANGAN',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
            ['KD_KELURAHAN' => '3517100021', 'KD_KECAMATAN' => '3517100', 'KELURAHAN' => 'MENTORO',  'ninput_oleh' => NULL, 'ninput_tgl' => NULL, 'nupdate_oleh' => NULL, 'nupdate_tgl' => NULL],
        ];


        foreach ($kelurahanData as $data) {
            Kelurahan::create($data);
        }
    }
}
