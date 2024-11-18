<?php

class JwtManager
{
    private $secretKey;
    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }
    public function createToken($payload)
    {
        $base64UrlHeader = $this->base64UrlEncode(json_encode(["alg" => "HS256", "typ" => "JWT"]));
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        $base64UrlSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secretKey, true);
        $base64UrlSignature = $this->base64UrlEncode($base64UrlSignature);
        return $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
    }

    private function base64UrlEncode($data)
    {
        $base64 = base64_encode($data);
        $base64Url = strtr($base64, '+/', '-_');
        return rtrim($base64Url, '=');
    }

    private function base64UrlDecode($data)
    {
        $base64 = strtr($data, '-_', '+/');
        $base64Padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);
        return base64_decode($base64Padded);
    }
    public function validateToken($token)
    {
        list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = explode('.', $token);

        $signature = $this->base64UrlDecode($base64UrlSignature);
        $expectedSignature = hash_hmac('sha256', $base64UrlHeader . '.' . $base64UrlPayload, $this->secretKey, true);

        return hash_equals($signature, $expectedSignature);
    }
    public function decodeToken($token)
    {
        list(, $base64UrlPayload,) = explode('.', $token);
        $payload = $this->base64UrlDecode($base64UrlPayload);
        return json_decode($payload, true);
    }
    public function checkToken()
    {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

        if (!$authHeader) {
            http_response_code(401);
            echo json_encode(["error" => "Authorization header missing"]);
            exit;
        }
        $tokenParts = explode(' ', $authHeader);
        if (count($tokenParts) !== 2 || $tokenParts[0] !== 'Bearer') {
            http_response_code(400);
            echo json_encode(["error" => "Invalid Authorization header format"]);
            exit;
        }

        $token = $tokenParts[1];

        if (!$this->validateToken($token)) {
            http_response_code(401);
            echo json_encode(["error" => "Invalid or expired token"]);
            exit;
        }

        $userData = $this->decodeToken($token);
        if (!$userData) {
            http_response_code(400);
            echo json_encode(["error" => "Malformed token"]);
            exit;
        }
        return $userData;
    }
}
