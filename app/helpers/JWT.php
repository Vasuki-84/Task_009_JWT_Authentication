<?php

class JWT
{
    // Generate JWT
    public static function generate($user)
    {
        $header = base64_encode(json_encode([
            "alg" => "HS256",
            "typ" => "JWT"
        ]));

        $payload = base64_encode(json_encode([
            "user_id" => $user['id'],
            "email" => $user['email'],
            "iat" => time(),
            "exp" => time() + $_ENV['JWT_EXPIRY']
        ]));

        $signature = hash_hmac(
            'sha256',
            "$header.$payload",
            $_ENV['JWT_SECRET'],
            true
        );

        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    // Validate JWT
    public static function validate($token)
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;

        $validSignature = base64_encode(
            hash_hmac(
                'sha256',
                "$header.$payload",
                $_ENV['JWT_SECRET'],
                true
            )
        );

        // Compare signatures
        if (
            !hash_equals(
                $validSignature,
                $signature
            )
        ) {
            return false;
        }

        $payloadData = json_decode(
            base64_decode($payload),
            true
        );

        // Check expiry
        if ($payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }
}