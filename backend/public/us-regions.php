<?php

require __DIR__ . '/../src/bootstrap.php';

try {
    $service = new UsRegionsService();
    $daterange = $_GET['daterange'] ?? null;

    JsonResponse::send($service->get($daterange));
} catch (InvalidArgumentException $e) {
    JsonResponse::error($e->getMessage(), 400);
} catch (Throwable $e) {
    JsonResponse::error('Unable to build us-regions response.', 500, [
        'detail' => $e->getMessage(),
    ]);
}