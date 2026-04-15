<?php

return [
    'logo' => [
        'start' => [
            'switch' => env('HEALTHATM_LOGO_START_SWITCH', 0),
            'image' => env('HEALTHATM_LOGO_START_IMAGE', ''),
        ],
        'company' => [
            'switch' => env('HEALTHATM_LOGO_COMPANY_SWITCH', 0),
            'image' => env('HEALTHATM_LOGO_COMPANY_IMAGE', ''),
        ],
        'mascot' => [
            'switch' => env('HEALTHATM_LOGO_MASCOT_SWITCH', 0),
            'image' => env('HEALTHATM_LOGO_MASCOT_IMAGE', ''),
        ],
    ],
];

