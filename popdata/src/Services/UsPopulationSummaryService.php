<?php

final class UsPopulationSummaryService
{
    private CensusApi $census;

    public function __construct(CensusApi $census)
    {
        $this->census = $census;
    }

    public function get(): array
    {
        $today = new DateTimeImmutable('today');

        $params = [
            'get' => 'MONTH,DATE_CODE,EDTMIDNIGHT,ESTMIDNIGHT,BIRTHCOMP,DEATHCOMP,TOTMIGCOMP,POPCOMP',
            'YEAR' => $today->format('Y'),
            'MONTH' => $today->format('n'),
            'DATE_CODE' => $today->format('j'),
        ];

        $rows = $this->census->getDailyPep($params);

        if (count($rows) < 2 || !isset($rows[0], $rows[1]) || !is_array($rows[1])) {
            throw new RuntimeException('Unexpected Census daily PEP response shape.');
        }

        $header = $rows[0];
        $values = $rows[1];

        $record = $this->combineHeaderAndRow($header, $values);

        $midnightPopulation = $this->pickMidnightPopulation($record);
        $birthInterval = $this->toPositiveFloat($record['BIRTHCOMP'] ?? null);
        $deathInterval = $this->toPositiveFloat($record['DEATHCOMP'] ?? null);
        $netMigInterval = $this->toPositiveFloat($record['TOTMIGCOMP'] ?? null);
        $netGainInterval = $this->toPositiveFloat($record['POPCOMP'] ?? null);

        return [
            'ok' => true,
            'sourceDate' => [
                'year' => (int) ($record['YEAR'] ?? $today->format('Y')),
                'month' => (int) ($record['MONTH'] ?? $today->format('n')),
                'day' => (int) ($record['DATE_CODE'] ?? $today->format('j')),
            ],
            'timezoneAtMidnight' => $this->detectTimezoneLabel($record),
            'baseEpochMs' => $this->midnightEpochMs($today),
            'basePopulation' => $midnightPopulation,
            'perSecond' => 1 / $netGainInterval,
            'secondsPerPersonNetGain' => $netGainInterval,
            'components' => [
                'birthEverySeconds' => $birthInterval,
                'deathEverySeconds' => $deathInterval,
                'netMigEverySeconds' => $netMigInterval,
                'birthPerSecond' => 1 / $birthInterval,
                'deathPerSecond' => 1 / $deathInterval,
                'netMigPerSecond' => 1 / $netMigInterval,
                'netPerSecondFromPopComp' => 1 / $netGainInterval,
            ],
        ];
    }

    private function combineHeaderAndRow(array $header, array $row): array
    {
        $combined = [];

        foreach ($header as $index => $name) {
            $combined[$name] = $row[$index] ?? null;
        }

        return $combined;
    }

    private function pickMidnightPopulation(array $record): int
    {
        $edt = isset($record['EDTMIDNIGHT']) ? (int) $record['EDTMIDNIGHT'] : 0;
        $est = isset($record['ESTMIDNIGHT']) ? (int) $record['ESTMIDNIGHT'] : 0;

        $value = max($edt, $est);

        if ($value <= 0) {
            throw new RuntimeException('Midnight population was missing from Census response.');
        }

        return $value;
    }

    private function detectTimezoneLabel(array $record): string
    {
        $edt = isset($record['EDTMIDNIGHT']) ? (int) $record['EDTMIDNIGHT'] : 0;
        $est = isset($record['ESTMIDNIGHT']) ? (int) $record['ESTMIDNIGHT'] : 0;

        return $edt >= $est ? 'EDT' : 'EST';
    }

    private function midnightEpochMs(DateTimeImmutable $today): int
    {
        return $today->getTimestamp() * 1000;
    }

    private function toPositiveFloat(mixed $value): float
    {
        $number = (float) $value;

        if ($number <= 0) {
            throw new RuntimeException('Expected a positive numeric interval from Census response.');
        }

        return $number;
    }
}