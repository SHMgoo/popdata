<?php

require __DIR__ . '/../src/bootstrap.php';

try {
    $service = new UsDensityService();
    $geo = $_GET['geo'] ?? null;

    JsonResponse::send($service->get($geo));
} catch (Throwable $e) {
    JsonResponse::error('Unable to build us-density response.', 500, [
        'detail' => $e->getMessage(),
    ]);
}