<?php

final class UsDensityService
{
    public function get(?string $geo = null): array
    {
        $file = __DIR__ . '/../../data/static/us-density.json';

        if (!is_file($file)) {
            throw new RuntimeException('Density data file is missing.');
        }

        $json = file_get_contents($file);

        if ($json === false) {
            throw new RuntimeException('Unable to read density data file.');
        }

        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new RuntimeException('Density data file contains invalid JSON.');
        }

        $payload = [
            'ok' => true,
            'source' => 'static',
            'states' => $data['states'] ?? [],
            'counties' => $data['counties'] ?? [],
            'cities' => $data['cities'] ?? [],
        ];

        if ($geo !== null) {
            $geo = strtolower($geo);

            $map = [
                'state' => 'states',
                'states' => 'states',
                'county' => 'counties',
                'counties' => 'counties',
                'city' => 'cities',
                'cities' => 'cities',
            ];

            if (!isset($map[$geo])) {
                throw new RuntimeException('Invalid geo parameter.');
            }

            $key = $map[$geo];

            return [
                'ok' => true,
                'source' => 'static',
                'geo' => $key,
                'items' => $payload[$key],
            ];
        }

        return $payload;
    }
}