<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Puppeteer Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la automatización con Puppeteer
    |
    */
    
    'executable_path' => env('PUPPETEER_EXECUTABLE_PATH', null),
    
    'options' => [
        'headless' => env('PUPPETEER_HEADLESS', true),
        'args' => [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--disable-gpu',
            '--window-size=1920,1080',
        ],
    ],
    
    'sat' => [
        'login_url' => 'https://cfdiau.sat.gob.mx/nidp/app/login',
        'timeout' => 30000, // 30 segundos
        'wait_after_action' => 2000, // 2 segundos entre acciones
    ],
];