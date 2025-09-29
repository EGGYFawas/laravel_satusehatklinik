<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Provinsi;

class ProvinsiSeeder extends Seeder
{
    public function run()
    {
        $provinsiData = [
            ['KD_PROVINSI' => '11', 'PROVINSI' => 'ACEH', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => 'all', 'nupdate_tgl' => '2013-07-11 08:54:23'],
            ['KD_PROVINSI' => '12', 'PROVINSI' => 'SUMATERA UTARA', 'ninput_oleh' => '0', 'ninput_tgl' => '2013-05-04', 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '13', 'PROVINSI' => 'SUMATERA BARAT', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '14', 'PROVINSI' => 'RIAU', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '15', 'PROVINSI' => 'JAMBI', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '16', 'PROVINSI' => 'SUMATERA SELATAN', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '17', 'PROVINSI' => 'BENGKULU', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '18', 'PROVINSI' => 'LAMPUNG', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '19', 'PROVINSI' => 'KEPULAUAN BANGKA BELITUNG', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '21', 'PROVINSI' => 'KEPULAUAN RIAU', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '31', 'PROVINSI' => 'DKI JAKARTA', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '32', 'PROVINSI' => 'JAWA BARAT', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '33', 'PROVINSI' => 'JAWA TENGAH', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '34', 'PROVINSI' => 'D I YOGYAKARTA', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '35', 'PROVINSI' => 'JAWA TIMUR', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '36', 'PROVINSI' => 'BANTEN', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '51', 'PROVINSI' => 'BALI', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '52', 'PROVINSI' => 'NUSA TENGGARA BARAT', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '53', 'PROVINSI' => 'NUSA TENGGARA TIMUR', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '61', 'PROVINSI' => 'KALIMANTAN BARAT', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '62', 'PROVINSI' => 'KALIMANTAN TENGAH', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '63', 'PROVINSI' => 'KALIMANTAN SELATAN', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '64', 'PROVINSI' => 'KALIMANTAN TIMUR', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '71', 'PROVINSI' => 'SULAWESI UTARA', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '72', 'PROVINSI' => 'SULAWESI TENGAH', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '73', 'PROVINSI' => 'SULAWESI SELATAN', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '74', 'PROVINSI' => 'SULAWESI TENGGARA', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '75', 'PROVINSI' => 'GORONTALO', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '76', 'PROVINSI' => 'SULAWESI BARAT', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '81', 'PROVINSI' => 'MALUKU', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '82', 'PROVINSI' => 'MALUKU UTARA', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '91', 'PROVINSI' => 'PAPUA', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
            ['KD_PROVINSI' => '92', 'PROVINSI' => 'PAPUA BARAT', 'ninput_oleh' => null, 'ninput_tgl' => null, 'nupdate_oleh' => null, 'nupdate_tgl' => null],
        ];

        foreach ($provinsiData as $data) {
            Provinsi::create($data);
        }
    }
}
