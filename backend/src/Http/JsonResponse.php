<?php

final class JsonResponse
{
    public static function send(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }

    public static function error(string $message, int $statusCode = 500, array $extra = []): void
    {
        self::send(array_merge([
            'ok' => false,
            'error' => $message,
        ], $extra), $statusCode);
    }
}