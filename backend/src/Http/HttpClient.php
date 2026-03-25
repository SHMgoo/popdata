<?php

final class HttpClient
{
    public function getJson(string $url, array $query = [], int $timeout = 15): array
    {
        $fullUrl = $url;

        if (!empty($query)) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $fullUrl .= $separator . http_build_query($query);
        }

        error_log('HTTP GET: ' . $fullUrl);

        $headers = [];
        $body = $this->requestWithRedirects($fullUrl, $timeout, $headers);

        $statusCode = $this->extractLastStatusCode($headers);

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException('Upstream request failed with status ' . $statusCode . '.');
        }

        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid JSON response from upstream.');
        }

        return $decoded;
    }

    private function requestWithRedirects(string $url, int $timeout, array &$finalHeaders, int $maxRedirects = 5): string
    {
        $currentUrl = $url;

        for ($i = 0; $i <= $maxRedirects; $i++) {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => $timeout,
                    'ignore_errors' => true,
                    'header' => [
                        'Accept: application/json',
                        'User-Agent: popdata/1.0',
                    ],
                ],
            ]);

            $body = @file_get_contents($currentUrl, false, $context);
            $headers = $http_response_header ?? [];

            if ($body === false) {
                throw new RuntimeException('HTTP request failed.');
            }

            $statusCode = $this->extractLastStatusCode($headers);

            if ($statusCode >= 300 && $statusCode < 400) {
                $location = $this->extractLocation($headers);

                if ($location === null) {
                    throw new RuntimeException('Upstream redirect missing Location header.');
                }

                $currentUrl = $this->resolveRedirectUrl($currentUrl, $location);
                continue;
            }

            $finalHeaders = $headers;
            return $body;
        }

        throw new RuntimeException('Too many upstream redirects.');
    }

    private function extractLastStatusCode(array $headers): int
    {
        $statusCode = 0;

        foreach ($headers as $header) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $header, $matches)) {
                $statusCode = (int) $matches[1];
            }
        }

        return $statusCode;
    }

    private function extractLocation(array $headers): ?string
    {
        foreach ($headers as $header) {
            if (stripos($header, 'Location:') === 0) {
                return trim(substr($header, 9));
            }
        }

        return null;
    }

    private function resolveRedirectUrl(string $currentUrl, string $location): string
    {
        if (preg_match('#^https?://#i', $location)) {
            return $location;
        }

        $parts = parse_url($currentUrl);

        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            throw new RuntimeException('Unable to resolve redirect URL.');
        }

        $scheme = $parts['scheme'];
        $host = $parts['host'];
        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        if (str_starts_with($location, '/')) {
            return $scheme . '://' . $host . $port . $location;
        }

        $path = $parts['path'] ?? '/';
        $dir = rtrim(str_replace('\\', '/', dirname($path)), '/');

        return $scheme . '://' . $host . $port . ($dir ? $dir : '') . '/' . $location;
    }
}