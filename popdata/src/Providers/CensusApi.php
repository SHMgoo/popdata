<?php

final class CensusApi
{
    private HttpClient $http;
    private array $servicesConfig;

    public function __construct(HttpClient $http, array $servicesConfig)
    {
        $this->http = $http;
        $this->servicesConfig = $servicesConfig;
    }

    public function getDailyPep(array $params): array
    {
        $baseUrl = $this->servicesConfig['census']['base_url'] ?? '';
        $apiKey = $this->servicesConfig['census']['api_key'] ?? '';

        if ($baseUrl === '') {
            throw new RuntimeException('Census base URL is not configured.');
        }

        if ($apiKey === '') {
            throw new RuntimeException('Census API key is not configured.');
        }

        $query = array_merge($params, [
            'key' => $apiKey,
        ]);

        return $this->http->getJson($baseUrl, $query);
    }

   public function getPepCharAge(int $year, array $params): array
    {
        $baseUrl = "https://api.census.gov/data/{$year}/pep/charage";

        $query = $params;

        $apiKey = $this->servicesConfig['census']['api_key'] ?? '';

        if ($apiKey !== '') {
            $query['key'] = $apiKey;
        }

        return $this->http->getJson($baseUrl, $query);
    }

}