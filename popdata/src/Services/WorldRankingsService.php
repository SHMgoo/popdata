<?php

final class WorldRankingsService
{
    public function get(?int $limit = null): array
    {
        $file = __DIR__ . '/../../data/static/world-rankings.json';

        if (!is_file($file)) {
            throw new RuntimeException('World rankings data file is missing.');
        }

        $json = file_get_contents($file);
        if ($json === false) {
            throw new RuntimeException('Unable to read world rankings data file.');
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new RuntimeException('World rankings data file contains invalid JSON.');
        }

        $countries = $data['countries'] ?? [];
        if (!is_array($countries)) {
            $countries = [];
        }

        if ($limit !== null && $limit > 0) {
            $countries = array_slice($countries, 0, $limit);
        }

        return [
            'ok' => true,
            'source' => $data['source'] ?? 'seed',
            'sourceNote' => $data['sourceNote'] ?? '',
            'countries' => $countries,
        ];
    }
}
