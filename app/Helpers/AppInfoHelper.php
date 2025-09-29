<?php

use App\Models\AppInfo;

if (!function_exists('getAppInfo')) {
    /**
     * Get application information from the database.
     *
     * @param string|null $key Specific key to retrieve (optional).
     * @return mixed Returns the requested value or all data if key is null.
     */
    function getAppInfo(string $key = null)
    {
        // Cache the data to optimize performance
        $appInfo = cache()->rememberForever('app_info', function () {
            return AppInfo::first();
        });

        // If a specific key is requested, return that key
        if ($key && $appInfo) {
            return $appInfo->$key ?? null;
        }

        return $appInfo;
    }
}
