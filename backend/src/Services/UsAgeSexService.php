<?php

final class UsAgeSexService
{
    public function get(?string $daterange = null): array
    {
        [$startYear, $endYear] = $this->parseDateRange($daterange);

        $file = __DIR__ . '/../../data/static/pyramid.json';

        if (!is_file($file)) {
            throw new RuntimeException('Age/sex data file is missing.');
        }

        $json = file_get_contents($file);

        if ($json === false) {
            throw new RuntimeException('Unable to read age/sex data file.');
        }

        $rows = json_decode($json, true);

        if (!is_array($rows) || count($rows) < 2) {
            throw new RuntimeException('Age/sex data file contains invalid JSON.');
        }

        $header = $rows[0];
        $records = [];

        for ($i = 1; $i < count($rows); $i++) {
            $records[] = $this->combineHeaderAndRow($header, $rows[$i]);
        }

        $recordsByYear = [];

        foreach ($records as $record) {
            $dateCode = (string) ($record['DATE_CODE'] ?? '');
            $year = $this->dateCodeToYear($dateCode);

            if ($year === null) {
                continue;
            }

            if ($year < $startYear || $year > $endYear) {
                continue;
            }

            $recordsByYear[$year][] = $record;
        }

        $maleValues = [];
        $femaleValues = [];
        $maxPercent = 0.0;
        $maxValue = 0;

        for ($year = $startYear; $year <= $endYear; $year++) {
            if (empty($recordsByYear[$year])) {
                throw new RuntimeException("Missing age/sex data for year {$year}.");
            }

            [$maleYear, $femaleYear, $yearMaxPercent, $yearMaxValue] = $this->buildYearSeries($year, $recordsByYear[$year]);

            $maleValues[] = $maleYear;
            $femaleValues[] = $femaleYear;
            $maxPercent = max($maxPercent, $yearMaxPercent);
            $maxValue = max($maxValue, $yearMaxValue);
        }

        return [
            'ok' => true,
            'source' => 'static',
            'daterange' => sprintf('%d0701-%d0701', $startYear, $endYear),
            'max_percent' => round($maxPercent, 6),
            'max_value' => $maxValue,
            'male' => [
                'label' => 'Male',
                'values' => $maleValues,
            ],
            'female' => [
                'label' => 'Female',
                'values' => $femaleValues,
            ],
        ];
    }

    private function parseDateRange(?string $daterange): array
    {
        if ($daterange === null || trim($daterange) === '') {
            return [2020, 2024];
        }

        if (!preg_match('/^(\d{4})0701-(\d{4})0701$/', trim($daterange), $m)) {
            throw new InvalidArgumentException('daterange must be in YYYY0701-YYYY0701 format.');
        }

        $startYear = (int) $m[1];
        $endYear = (int) $m[2];

        if ($startYear > $endYear) {
            throw new InvalidArgumentException('daterange start year must be less than or equal to end year.');
        }

        return [$startYear, $endYear];
    }

    private function combineHeaderAndRow(array $header, array $row): array
    {
        $combined = [];

        foreach ($header as $index => $name) {
            $combined[$name] = $row[$index] ?? null;
        }

        return $combined;
    }

    private function buildYearSeries(int $year, array $records): array
    {
        $bySex = [
            1 => [
                'label' => 'Male',
                'ages' => [],
                'total' => 0,
            ],
            2 => [
                'label' => 'Female',
                'ages' => [],
                'total' => 0,
            ],
        ];

        foreach ($records as $record) {
            $sex = (int) ($record['SEX'] ?? 0);
            $age = (int) ($record['AGE'] ?? -1);
            $pop = (int) ($record['POP'] ?? 0);

            if (!isset($bySex[$sex])) {
                continue;
            }

            if ($age === 999) {
                $bySex[$sex]['total'] = $pop;
                continue;
            }

            if ($age < 0) {
                continue;
            }

            $bySex[$sex]['ages'][$age] = $pop;
        }

        $maleTotal = $bySex[1]['total'];
        $femaleTotal = $bySex[2]['total'];
        $overallTotal = $maleTotal + $femaleTotal;

        if ($maleTotal <= 0 || $femaleTotal <= 0 || $overallTotal <= 0) {
            throw new RuntimeException("Missing total population rows for year {$year}.");
        }

        $maleAgeGroups = [];
        $femaleAgeGroups = [];
        $yearMaxPercent = 0.0;
        $yearMaxValue = 0;
        $maleMaxGender = 0.0;
        $maleMaxTotal = 0.0;
        $maleMaxActual = 0;
        $femaleMaxGender = 0.0;
        $femaleMaxTotal = 0.0;
        $femaleMaxActual = 0;

        for ($age = 0; $age <= 100; $age++) {
            $malePop = (int) ($bySex[1]['ages'][$age] ?? 0);
            $femalePop = (int) ($bySex[2]['ages'][$age] ?? 0);

            $maleGenderPct = $malePop / $maleTotal;
            $maleTotalPct = $malePop / $overallTotal;
            $femaleGenderPct = $femalePop / $femaleTotal;
            $femaleTotalPct = $femalePop / $overallTotal;

            $maleAgeGroups[] = [
                'age' => str_pad((string) $age, 2, '0', STR_PAD_LEFT),
                'gender_percentage' => round($maleGenderPct, 6),
                'total_percentage' => round($maleTotalPct, 6),
                'actual_value' => $malePop,
            ];

            $femaleAgeGroups[] = [
                'age' => str_pad((string) $age, 2, '0', STR_PAD_LEFT),
                'gender_percentage' => round($femaleGenderPct, 6),
                'total_percentage' => round($femaleTotalPct, 6),
                'actual_value' => $femalePop,
            ];

            $maleMaxGender = max($maleMaxGender, $maleGenderPct);
            $maleMaxTotal = max($maleMaxTotal, $maleTotalPct);
            $maleMaxActual = max($maleMaxActual, $malePop);

            $femaleMaxGender = max($femaleMaxGender, $femaleGenderPct);
            $femaleMaxTotal = max($femaleMaxTotal, $femaleTotalPct);
            $femaleMaxActual = max($femaleMaxActual, $femalePop);

            $yearMaxPercent = max($yearMaxPercent, $maleGenderPct, $femaleGenderPct);
            $yearMaxValue = max($yearMaxValue, $malePop, $femalePop);
        }

        $timestamp = gmmktime(0, 0, 0, 7, 1, $year);

        $male = [
            'date' => $timestamp,
            'max_gender_percentage' => round($maleMaxGender, 6),
            'max_total_percentage' => round($maleMaxTotal, 6),
            'max_actual_value' => $maleMaxActual,
            'age_groups' => $maleAgeGroups,
        ];

        $female = [
            'date' => $timestamp,
            'max_gender_percentage' => round($femaleMaxGender, 6),
            'max_total_percentage' => round($femaleMaxTotal, 6),
            'max_actual_value' => $femaleMaxActual,
            'age_groups' => $femaleAgeGroups,
        ];

        return [$male, $female, $yearMaxPercent, $yearMaxValue];
    }

    private function dateCodeToYear(string $code): ?int
    {
        $map = [
            '3' => 2010,
            '4' => 2011,
            '5' => 2012,
            '6' => 2013,
            '7' => 2014,
            '8' => 2015,
            '9' => 2016,
            '10' => 2017,
            '11' => 2018,
            '12' => 2019,
            '13' => 2020,
            '14' => 2021,
            '15' => 2022,
            '16' => 2023,
            '17' => 2024,
            '18' => 2025,
        ];

        return $map[$code] ?? null;
    }
}