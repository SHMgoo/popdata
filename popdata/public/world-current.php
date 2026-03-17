<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require __DIR__ . '/../src/bootstrap.php';

try {
    $service = new WorldCurrentService();
    JsonResponse::send($service->get());
} catch (Throwable $e) {
    JsonResponse::error('Unable to build world-current response.', 500, [
        'detail' => $e->getMessage(),
    ]);
}
