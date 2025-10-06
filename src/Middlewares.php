<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Middlewares
{
    public static function cors(Request $request, Response &$response, callable $next): Response
    {
        $response = $next($request, $response);
        return self::withCors($response);
    }

    public static function options(Request $request, Response $response): Response
    {
        return self::withCors($response->withStatus(204));
    }

    private static function withCors(Response $response): Response
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, X-API-Key')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
    }

    public static function auth(Request $request, Response $response, callable $next): Response
    {
        $method = strtoupper($request->getMethod());
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $key = $request->getHeader('X-API-Key');
            if (!$key || $key !== (getenv('API_KEY') ?: '')) {
                $response->getBody()->write(json_encode(['error' => 'Unauthorized.']));
                return self::withCors($response->withStatus(401));
            }
        }
        return $next($request, $response);
    }
}