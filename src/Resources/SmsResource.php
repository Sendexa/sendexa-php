<?php

declare(strict_types=1);

namespace Sendexa\Resources;

use Sendexa\HttpClient;

class SmsResource
{
    public function __construct(private readonly HttpClient $client) {}

    /**
     * Send a single SMS message.
     *
     * @param array<string, mixed> $options  Additional options (callbackUrl, etc.)
     * @return array<string, mixed>
     */
    public function send(string $to, string $from, string $message, array $options = []): array
    {
        return $this->client->post('/v1/sms/send', array_merge([
            'to'      => $to,
            'from'    => $from,
            'message' => $message,
        ], $options));
    }

    /**
     * Send SMS to multiple recipients in one request.
     *
     * Each item in $messages must have a 'to' key. A 'message' key is optional
     * and overrides the shared $message for that recipient.
     *
     * @param list<array<string, string>> $messages
     * @param array<string, mixed>        $options
     * @return array<string, mixed>
     */
    public function sendBulk(string $from, array $messages, string $message = '', array $options = []): array
    {
        return $this->client->post('/v1/sms/bulk', array_merge([
            'from'     => $from,
            'message'  => $message,
            'messages' => $messages,
        ], $options));
    }

    /**
     * Retrieve the delivery status of a sent message.
     *
     * @return array<string, mixed>
     */
    public function getStatus(string $messageId): array
    {
        return $this->client->get("/v1/sms/status/{$messageId}");
    }

    /**
     * Resend a previously failed or undelivered message.
     *
     * @return array<string, mixed>
     */
    public function resend(string $messageId): array
    {
        return $this->client->post("/v1/sms/resend/{$messageId}");
    }
}
