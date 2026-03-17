<?php

return [
    'census' => [
        'base_url' => 'https://api.census.gov/data/restricted/pep/daily',
        'api_key' => getenv('CENSUS_API_KEY') ?: '',
    ],
];