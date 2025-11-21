<?php

namespace App\Libraries;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class JWT
{
    private string $key;
    private string $algorithm = 'HS256';

    public function __construct()
    {
        $this->key = getenv('JWT_SECRET') ?: 'my_super_secret_key';
    }

    // Generate token
    public function generate(array $payload): string
    {
        $issuedAt = time();
        $expire = $issuedAt + 3600; // 1 hour expiry
        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expire;

        return FirebaseJWT::encode($payload, $this->key, $this->algorithm);
    }

    // Validate token
    public function validate(string $token): ?object
    {
        try {
            return FirebaseJWT::decode($token, new Key($this->key, $this->algorithm));
        } catch (\Exception $e) {
            return null;
        }
    }

    // Add this missing decode method
    public function decode(string $token): ?object
    {
        return $this->validate($token);
    }
}
