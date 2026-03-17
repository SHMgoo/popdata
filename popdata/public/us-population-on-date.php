<?php

header('Access-Control-Allow-Origin: http://127.0.0.1:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require __DIR__ . '/../src/bootstrap.php';

$date = trim((string) ($_GET['date'] ?? ''));

if ($date === '') {
    JsonResponse::error('Missing required query parameter: date', 400);
}

try {
    $http = new HttpClient();
    $census = new CensusApi($http, $servicesConfig);
    $service = new UsPopulationOnDateService($census);

    JsonResponse::send($service->getByDate($date));
} catch (InvalidArgumentException $e) {
    JsonResponse::error($e->getMessage(), 400);
} catch (RuntimeException $e) {
    JsonResponse::error($e->getMessage(), 502);
} catch (Throwable $e) {
    JsonResponse::error('Unable to build us-population-on-date response.', 500, [
        'detail' => $e->getMessage(),
    ]);
}