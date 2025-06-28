<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Observer'ın hangi User model'ini izleyeceği
    |
    */
    'user_model' => env('CACHEABLE_PERMISSIONS_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Permission Cache TTL
    |--------------------------------------------------------------------------
    |
    | Permission kontrolleri için cache süresi (dakika cinsinden)
    |
    */
    'permission_cache_ttl' => env('PERMISSION_CACHE_TTL', 15),

    /*
    |--------------------------------------------------------------------------
    | Role Cache TTL
    |--------------------------------------------------------------------------
    |
    | Role kontrolleri için cache süresi (saniye cinsinden)
    |
    */
    'role_cache_ttl' => env('ROLE_CACHE_TTL', 900),

    /*
    |--------------------------------------------------------------------------
    | Cache Driver
    |--------------------------------------------------------------------------
    |
    | Hangi cache driver'ının kullanılacağı
    |
    */
    'cache_driver' => env('CACHEABLE_PERMISSIONS_DRIVER', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Clear All Cache on Permission Change
    |--------------------------------------------------------------------------
    |
    | Permission değiştiğinde tüm cache'i temizleyip temizlememe
    |
    */
    'clear_all_on_permission_change' => env('CLEAR_ALL_ON_PERMISSION_CHANGE', false),

    /*
    |--------------------------------------------------------------------------
    | Clear All Cache on Role Change
    |--------------------------------------------------------------------------
    |
    | Role değiştiğinde tüm cache'i temizleyip temizlememe
    |
    */
    'clear_all_on_role_change' => env('CLEAR_ALL_ON_ROLE_CHANGE', false),

    /*
    |--------------------------------------------------------------------------
    | Auto Clear Cache
    |--------------------------------------------------------------------------
    |
    | Model değişikliklerinde otomatik cache temizleme
    |
    */
    'auto_clear_cache' => env('AUTO_CLEAR_CACHE', true),
];