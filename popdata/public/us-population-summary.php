<?php

header('Access-Control-Allow-Origin: http://127.0.0.1:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require __DIR__ . '/../src/bootstrap.php';

try {
    $http = new HttpClient();
    $census = new CensusApi($http, $servicesConfig);
    $service = new UsPopulationSummaryService($census);

    JsonResponse::send($service->get());
} catch (Throwable $e) {
    JsonResponse::error('Unable to build us-population-summary response.', 500, [
        'detail' => $e->getMessage(),
    ]);
}