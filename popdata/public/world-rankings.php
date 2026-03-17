<?php

header('Access-Control-Allow-Origin: http://127.0.0.1:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require __DIR__ . '/../src/bootstrap.php';

try {
    $service = new WorldRankingsService();

    $limit = null;
    if (isset($_GET['limit']) && $_GET['limit'] !== '') {
        $limit = (int) $_GET['limit'];
        if ($limit <= 0) {
            throw new RuntimeException('Invalid limit parameter.');
        }
    }

    JsonResponse::send($service->get($limit));
} catch (Throwable $e) {
    JsonResponse::error('Unable to build world-rankings response.', 500, [
        'detail' => $e->getMessage(),
    ]);
}
