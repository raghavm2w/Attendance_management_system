<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateAccessToken(array $payload): string
{
    $issuedAt = time();
    $expire   = $issuedAt + (int) getenv('JWT_ACCESS_TTL');

    $token = [
        'iss' => getenv('JWT_ISSUER'),
        'aud' => getenv('JWT_AUDIENCE'),
        'iat' => $issuedAt,
        'exp' => $expire,
        'data' => $payload
    ];

    return JWT::encode($token, getenv('JWT_SECRET'), 'HS256');
}
function generateRefreshToken(array $payload):string
{
     $issuedAt = time();
    $expire   = $issuedAt + (int) getenv('JWT_REFRESH_TTL');

    $token = [
        'iss' => getenv('JWT_ISSUER'),
        'aud' => getenv('JWT_AUDIENCE'),
        'iat' => $issuedAt,
        'exp' => $expire,
        'data' => $payload
    ];

    return JWT::encode($token, getenv('JWT_SECRET'), 'HS256');
}

function verifyToken(string $token)
{
    return JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));
}
