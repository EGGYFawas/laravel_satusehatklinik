<?php

use App\Models\Satusehat;

if (!function_exists('loadSatusehatConfig')) {
    function loadSatusehatConfig()
    {
        $satusehat = Satusehat::first();

        if ($satusehat) {
            config([
                'services.satusehat.client_id' => $satusehat->client_id,
                'services.satusehat.client_secret' => $satusehat->client_secret,
                'services.satusehat.organization_id' => $satusehat->organization_id,
                'services.satusehat.environment' => $satusehat->environment,
            ]);
        }
    }
}
