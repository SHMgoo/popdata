<?php

final class WorldCurrentService
{
    public function get(): array
    {
        $file = __DIR__ . '/../../data/static/world-current.json';

        if (!is_file($file)) {
            throw new RuntimeException('World current data file is missing.');
        }

        $json = file_get_contents($file);
        if ($json === false) {
            throw new RuntimeException('Unable to read world current data file.');
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new RuntimeException('World current data file contains invalid JSON.');
        }

        // Validate required fields exist
        foreach (['baseEpochMs', 'basePopulation', 'perSecond'] as $k) {
            if (!array_key_exists($k, $data)) {
                throw new RuntimeException("Missing required field: {$k}");
            }
        }

        return [
            'ok' => true,
            'source' => $data['source'] ?? 'seed',
            'sourceNote' => $data['sourceNote'] ?? '',
            'baseEpochMs' => (int) $data['baseEpochMs'],
            'basePopulation' => (int) $data['basePopulation'],
            'perSecond' => (float) $data['perSecond'],
        ];
    }
}
