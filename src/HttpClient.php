<?php

declare(strict_types=1);

namespace Sendexa;

class HttpClient
{
    private readonly string $auth;
    private readonly string $baseUrl;
    private readonly float $timeout;

    private const USER_AGENT = 'sendexa-php/0.1.0';
    private const DEFAULT_BASE_URL = 'https://api.sendexa.co';
    private const DEFAULT_TIMEOUT = 30.0;

    public function __construct(
        ?string $apiKey = null,
        ?string $apiSecret = null,
        ?string $token = null,
        string $baseUrl = self::DEFAULT_BASE_URL,
        float $timeout = self::DEFAULT_TIMEOUT,
    ) {
        if ($token !== null) {
            $this->auth = 'Basic ' . $token;
        } elseif ($apiKey !== null && $apiSecret !== null) {
            $this->auth = 'Basic ' . base64_encode("{$apiKey}:{$apiSecret}");
        } else {
            throw new \InvalidArgumentException(
                "Provide either 'token' or both 'apiKey' and 'apiSecret'."
            );
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }

    // -------------------------------------------------------------------------
    // Core request
    // -------------------------------------------------------------------------

    /** @return array<string, mixed> */
    public function request(string $method, string $path, mixed $body = null): array
    {
        $url = $this->baseUrl . $path;
        $ch  = curl_init($url);

        if ($ch === false) {
            throw new SendexaException('Failed to initialise cURL', 0, 'CURL_INIT_FAILED');
        }

        $headers = [
            'Authorization: ' . $this->auth,
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: ' . self::USER_AGENT,
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => (int) $this->timeout,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR));
        }

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new SendexaException(
                $curlErr ?: 'cURL request failed',
                0,
                'REQUEST_FAILED',
            );
        }

        /** @var array<string, mixed>|null $payload */
        $payload = json_decode((string) $raw, true);

        if ($httpCode >= 400) {
            $message   = is_array($payload) ? (string) ($payload['message'] ?? "HTTP {$httpCode}") : "HTTP {$httpCode}";
            $code      = is_array($payload) ? (string) ($payload['code']    ?? 'UNKNOWN_ERROR')    : 'UNKNOWN_ERROR';
            $requestId = is_array($payload) ? ($payload['requestId'] ?? $payload['request_id'] ?? null) : null;
            throw new SendexaException($message, $httpCode, $code, is_string($requestId) ? $requestId : null, $payload);
        }

        return is_array($payload) ? $payload : [];
    }

    // -------------------------------------------------------------------------
    // Convenience methods
    // -------------------------------------------------------------------------

    /** @return array<string, mixed> */
    public function get(string $path): array
    {
        return $this->request('GET', $path);
    }

    /**
     * @param array<string, mixed>|null $body
     * @return array<string, mixed>
     */
    public function post(string $path, ?array $body = null): array
    {
        return $this->request('POST', $path, $body);
    }

    /** @return array<string, mixed> */
    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }
}
