<?php

final class UsRegionsService
{
    public function get(?string $daterange = null): array
    {
        $file = __DIR__ . '/../../data/static/us-regions.json';

        if (!is_file($file)) {
            throw new RuntimeException('Regions data file is missing.');
        }

        $json = file_get_contents($file);

        if ($json === false) {
            throw new RuntimeException('Unable to read regions data file.');
        }

        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new RuntimeException('Regions data file contains invalid JSON.');
        }

        [$startYear, $endYear] = $this->parseDateRange($daterange, $data);

        $result = [
            'ok' => true,
            'source' => 'static',
            'daterange' => sprintf('%d0701-%d0701', $startYear, $endYear),
            'northeast' => [
                'label' => $data['northeast']['label'] ?? 'Northeast',
                'values' => $this->filterValues($data['northeast']['values'] ?? [], $startYear, $endYear),
            ],
            'midwest' => [
                'label' => $data['midwest']['label'] ?? 'Midwest',
                'values' => $this->filterValues($data['midwest']['values'] ?? [], $startYear, $endYear),
            ],
            'south' => [
                'label' => $data['south']['label'] ?? 'South',
                'values' => $this->filterValues($data['south']['values'] ?? [], $startYear, $endYear),
            ],
            'west' => [
                'label' => $data['west']['label'] ?? 'West',
                'values' => $this->filterValues($data['west']['values'] ?? [], $startYear, $endYear),
            ],
        ];

        $result['max_percent'] = $this->maxPercent($result);

        return $result;
    }

    private function parseDateRange(?string $daterange, array $data): array
    {
        $years = array_map(
            fn(array $row) => (int) $row['year'],
            $data['south']['values'] ?? []
        );

        if (empty($years)) {
            throw new RuntimeException('Regions data file contains no values.');
        }

        sort($years);
        $minYear = $years[0];
        $maxYear = $years[count($years) - 1];

        if ($daterange === null || trim($daterange) === '') {
            return [$minYear, $maxYear];
        }

        if (!preg_match('/^(\d{4})0701-(\d{4})0701$/', trim($daterange), $m)) {
            throw new InvalidArgumentException('daterange must be in YYYY0701-YYYY0701 format.');
        }

        $startYear = (int) $m[1];
        $endYear = (int) $m[2];

        if ($startYear > $endYear) {
            throw new InvalidArgumentException('daterange start year must be less than or equal to end year.');
        }

        if ($startYear < $minYear || $endYear > $maxYear) {
            throw new InvalidArgumentException(
                sprintf('daterange must be between %d0701 and %d0701.', $minYear, $maxYear)
            );
        }

        return [$startYear, $endYear];
    }

    private function filterValues(array $values, int $startYear, int $endYear): array
    {
        return array_values(array_filter(
            $values,
            fn(array $row) => (int) $row['year'] >= $startYear && (int) $row['year'] <= $endYear
        ));
    }

    private function maxPercent(array $result): float
    {
        $max = 0.0;

        foreach (['northeast', 'midwest', 'south', 'west'] as $region) {
            foreach ($result[$region]['values'] as $row) {
                $percent = (float) ($row['percentage'] ?? 0);
                if ($percent > $max) {
                    $max = $percent;
                }
            }
        }

        return round($max, 4);
    }
}