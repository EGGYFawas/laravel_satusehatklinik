<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'logo',
        'primary_color', 'secondary_color', 
        'hero_title', 'hero_image', 
        'why_us_image', 
        'about_us_title', 'about_us_description', 'about_us_image',
        'midtrans_server_key', 'midtrans_client_key',
        'satusehat_client_id', 'satusehat_client_secret', 'satusehat_organization_id',
        'bpjs_cons_id', 'bpjs_secret_key', 'bpjs_user_key',
    ];

    /**
     * Sesuai Risk Analysis di PDF: Enkripsi API Keys.
     */
    protected $casts = [
        'midtrans_server_key' => 'encrypted',
        'midtrans_client_key' => 'encrypted',
        'satusehat_client_id' => 'encrypted',
        'satusehat_client_secret' => 'encrypted',
        'bpjs_cons_id' => 'encrypted',
        'bpjs_secret_key' => 'encrypted',
        'bpjs_user_key' => 'encrypted',
    ];
}