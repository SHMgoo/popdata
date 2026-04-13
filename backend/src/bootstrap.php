<?php

declare(strict_types=1);

date_default_timezone_set('America/New_York');

require __DIR__ . '/Http/JsonResponse.php';
require __DIR__ . '/Http/HttpClient.php';
require __DIR__ . '/Providers/CensusApi.php';
require __DIR__ . '/Services/UsPopulationSummaryService.php';
require __DIR__ . '/Services/UsRegionsService.php';
require __DIR__ . '/Services/WorldCurrentService.php';
require __DIR__ . '/Services/WorldRankingsService.php';
require __DIR__ . '/Services/UsPopulationOnDateService.php';
require __DIR__ . '/Services/UsPopulousService.php';
require __DIR__ . '/Services/UsDensityService.php';
require __DIR__ . '/Services/UsAgeSexService.php';


$appConfig = require __DIR__ . '/../config/app.php';
$servicesConfig = require __DIR__ . '/../config/services.php';