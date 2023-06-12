<?php
namespace Phpnova\Next\Utils;

use Firebase\JWT\JWT as fbJWT;
use Firebase\JWT\Key;

class jwt
{
    private static string $key = '';

    public static function encode(array $payload): string
    {
        return fbJWT::encode($payload, self::$key, 'HS256');
    }

    public static function decote(string $token): object
    {
        return fbJWT::decode($token, new Key(self::$key, 'HS256'));
    }

    public static function verify(string $token): bool
    {
        try {
            self::decote($token);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}