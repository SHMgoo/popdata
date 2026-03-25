<?php

require __DIR__ . '/../src/bootstrap.php';

JsonResponse::send([
    'ok' => true,
    'service' => $appConfig['name'] ?? 'popdata',
    'endpoints' => [
        '/health.php',
        '/manifest.php',
        '/us-population-summary.php',        
        '/us-regions.php',        
        '/us-population-on-date.php',
        '/us-populous.php',
        '/us-density.php',
        '/us-age-sex.php',
        '/world-current.php',
        '/world-rankings.php',
    ],
]);