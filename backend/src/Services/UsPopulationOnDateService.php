<?php

final class UsPopulationOnDateService
{
    private CensusApi $census;

    public function __construct(CensusApi $census)
    {
        $this->census = $census;
    }

    public function getByDate(string $date): array
    {
        $requestedDate = $this->parseDate($date);

        $params = [
            'get' => 'MONTH,DATE_CODE,EDTMIDNIGHT,ESTMIDNIGHT',
            'YEAR' => $requestedDate->format('Y'),
            'MONTH' => $requestedDate->format('n'),
            'DATE_CODE' => $requestedDate->format('j'),
        ];

        $rows = $this->census->getDailyPep($params);

        if (count($rows) < 2 || !isset($rows[0], $rows[1]) || !is_array($rows[1])) {
            throw new RuntimeException('Unexpected Census daily PEP response shape.');
        }

        $record = $this->combineHeaderAndRow($rows[0], $rows[1]);
        $population = $this->pickPopulation($record);
        $timezone = $this->detectTimezoneLabel($record);

        return [
            'ok' => true,
            'data' => [
                'date' => $requestedDate->format('Y-m-d'),
                'legacyDate' => $requestedDate->format('Ymd'),
                'label' => $requestedDate->format('F j, Y'),
                'population' => $population,
                'formattedPopulation' => number_format($population),
                'timezoneAtMidnight' => $timezone,
                'source' => 'U.S. Census Bureau PEP Daily',
                'request' => [
                    'year' => (int) $requestedDate->format('Y'),
                    'month' => (int) $requestedDate->format('n'),
                    'day' => (int) $requestedDate->format('j'),
                ],
            ],
        ];
    }

    private function parseDate(string $date): DateTimeImmutable
    {
        $value = trim($date);

        if ($value === '') {
            throw new InvalidArgumentException('Date is required.');
        }

        $formats = ['!Y-m-d', '!Ymd'];

        foreach ($formats as $format) {
            $parsed = DateTimeImmutable::createFromFormat($format, $value);
            $errors = DateTimeImmutable::getLastErrors();

            $hasErrors = $parsed === false
                || ($errors !== false && (
                    ($errors['warning_count'] ?? 0) > 0 ||
                    ($errors['error_count'] ?? 0) > 0
                ));

            if (! $hasErrors) {
                return $parsed;
            }
        }

        throw new InvalidArgumentException('Date must be in YYYY-MM-DD or YYYYMMDD format.');
    }

    private function combineHeaderAndRow(array $header, array $row): array
    {
        $combined = [];

        foreach ($header as $index => $name) {
            $combined[$name] = $row[$index] ?? null;
        }

        return $combined;
    }

    private function pickPopulation(array $record): int
    {
        $edt = isset($record['EDTMIDNIGHT']) ? (int) $record['EDTMIDNIGHT'] : 0;

        if ($edt <= 0) {
            throw new RuntimeException('Population was missing from Census response.');
        }

        return $edt;
    }

    private function detectTimezoneLabel(array $record): string
    {
        $edt = isset($record['EDTMIDNIGHT']) ? (int) $record['EDTMIDNIGHT'] : 0;
        $est = isset($record['ESTMIDNIGHT']) ? (int) $record['ESTMIDNIGHT'] : 0;

        return $edt >= $est ? 'EDT' : 'EST';
    }
}