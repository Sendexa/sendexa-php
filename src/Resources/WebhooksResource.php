<?php

declare(strict_types=1);

namespace Sendexa\Resources;

use Sendexa\SendexaException;

/**
 * Helpers for verifying and parsing Sendexa webhook events.
 *
 * Pure functions — no HTTP client needed. Works in any PHP 8.1+ environment.
 */
class WebhooksResource
{
    /**
     * Verify the X-Sendexa-Signature header on an incoming webhook request.
     *
     * Sendexa signs every webhook payload with HMAC-SHA256 using your webhook
     * secret. Always verify before processing events.
     *
     * @example
     * $sig   = $_SERVER['HTTP_X_SENDEXA_SIGNATURE'] ?? '';
     * $valid = $client->webhooks->verify($sig, file_get_contents('php://input'), $_ENV['SENDEXA_WEBHOOK_SECRET']);
     * if (!$valid) { http_response_code(401); exit; }
     */
    public function verify(string $signature, string $rawBody, string $secret): bool
    {
        if ($signature === '' || $secret === '') {
            return false;
        }

        // Strip the optional "sha256=" prefix
        $sigHex  = str_starts_with($signature, 'sha256=')
            ? substr($signature, 7)
            : $signature;

        $expected = hash_hmac('sha256', $rawBody, $secret);

        return hash_equals($expected, $sigHex);
    }

    /**
     * Parse a raw webhook body into an associative array.
     *
     * Does NOT verify the signature — call verify() first.
     *
     * @return array<string, mixed>
     * @throws SendexaException if the payload is not valid JSON or missing the "event" field
     */
    public function parse(string $rawBody): array
    {
        /** @var array<string, mixed>|null $payload */
        $payload = json_decode($rawBody, true);

        if (!is_array($payload)) {
            throw new SendexaException(
                'Webhook payload is not valid JSON',
                400,
                'INVALID_WEBHOOK_PAYLOAD',
            );
        }

        if (!array_key_exists('event', $payload)) {
            throw new SendexaException(
                'Webhook payload is missing the "event" field',
                400,
                'INVALID_WEBHOOK_PAYLOAD',
            );
        }

        return $payload;
    }

    /**
     * Return true if the parsed event matches the given event type.
     *
     * @param array<string, mixed> $event
     *
     * @example
     * $event = $client->webhooks->parse($rawBody);
     * if ($client->webhooks->isEvent($event, 'message.delivered')) {
     *     echo $event['data']['messageId'];
     * }
     */
    public function isEvent(array $event, string $eventType): bool
    {
        return ($event['event'] ?? null) === $eventType;
    }
}
