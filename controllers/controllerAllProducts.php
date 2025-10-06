<?php
declare(strict_types=1);

namespace App\Controllers;

use ErrorException;

class ProductsController
{
    public static function fetchAll(): array
    {
        $url = 'http://localhost/api/products';

        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
                'ignore_errors' => true,
                'header' => 'Accept: application/json\r\n',
            ],
        ]);

        $body = @file_get_contents($url, false, $ctx);

        if ($body === false) {
            $er = error_get_last()['message'] ?? 'Unknown error';
            throw new \RuntimeException('API returned error: ' . $er);
        }

        $statusLine = isset(['http_response_header'][0]) ? $http_response_header[0] : '';
        if (!preg_match('~HTTP/\d\.\d\s+(\d{3})~', $statusLine, $m)) {
            throw new \RuntimeException("Cannot parse HTTP status: '$statusLine'");
        }

        $code = (int)$m[1];
        if ($code !== 200) {
            throw new \RuntimeException("HTTP status code: $body");
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("JSON parsing error: " . json_last_error_msg());
        }

        return is_array($data) ? $data : [];
    }
}
