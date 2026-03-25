<?php

require __DIR__ . '/../src/bootstrap.php';

JsonResponse::send([
    'ok' => true,
    'service' => $appConfig['name'] ?? 'popdata',
    'env' => $appConfig['env'] ?? 'local',
]);