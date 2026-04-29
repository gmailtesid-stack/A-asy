<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    | Credentials didapat dari: https://console.cloudinary.com/
    */
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', ''),
    'api_key'    => env('CLOUDINARY_API_KEY', ''),
    'api_secret' => env('CLOUDINARY_API_SECRET', ''),

    // Upload preset (opsional, untuk unsigned upload)
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET', 'easy_pos_products'),

    // Default folder
    'folder' => env('CLOUDINARY_FOLDER', 'easy-pos'),
];
