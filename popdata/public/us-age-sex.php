<?php

require __DIR__ . '/../src/bootstrap.php';

try {
    $service = new UsAgeSexService();

    $daterange = $_GET['daterange'] ?? null;

    JsonResponse::send($service->get($daterange));
} catch (InvalidArgumentException $e) {
    JsonResponse::error($e->getMessage(), 400);
} catch (RuntimeException $e) {
    JsonResponse::error($e->getMessage(), 502);
} catch (Throwable $e) {
    JsonResponse::error('Unable to build us-age-sex response.', 500, [
        'detail' => $e->getMessage(),
    ]);
}