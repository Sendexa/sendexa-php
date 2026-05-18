<?php

declare(strict_types=1);

namespace Sendexa\Resources;

use Sendexa\HttpClient;

class EmailResource
{
    public function __construct(private readonly HttpClient $client) {}

    /**
     * Send a single transactional or marketing email.
     *
     * @param array<string, mixed> $options  Additional options (replyTo, attachments, metadata, etc.)
     * @return array<string, mixed>
     */
    public function send(
        string $to,
        string $from,
        string $subject,
        ?string $html = null,
        ?string $text = null,
        array $options = [],
    ): array {
        $body = array_merge([
            'to'      => $to,
            'from'    => $from,
            'subject' => $subject,
        ], $options);

        if ($html !== null) {
            $body['html'] = $html;
        }
        if ($text !== null) {
            $body['text'] = $text;
        }

        return $this->client->post('/v1/email/send', $body);
    }

    /**
     * Send the same email to multiple recipients.
     *
     * Each item in $messages must have a 'to' key and may include a 'subject'
     * or 'variables' override.
     *
     * @param list<array<string, mixed>> $messages
     * @param array<string, mixed>       $options
     * @return array<string, mixed>
     */
    public function sendBulk(
        string $from,
        string $subject,
        array $messages,
        ?string $html = null,
        ?string $text = null,
        array $options = [],
    ): array {
        $body = array_merge([
            'from'     => $from,
            'subject'  => $subject,
            'messages' => $messages,
        ], $options);

        if ($html !== null) {
            $body['html'] = $html;
        }
        if ($text !== null) {
            $body['text'] = $text;
        }

        return $this->client->post('/v1/email/bulk', $body);
    }

    /**
     * Send an email using a pre-built dashboard template.
     *
     * @param array<string, mixed> $variables Template variable substitutions
     * @param array<string, mixed> $options   Additional options
     * @return array<string, mixed>
     */
    public function sendWithTemplate(
        string $to,
        string $from,
        string $templateId,
        array $variables = [],
        array $options = [],
    ): array {
        $body = array_merge([
            'to'         => $to,
            'from'       => $from,
            'templateId' => $templateId,
        ], $options);

        if (!empty($variables)) {
            $body['variables'] = $variables;
        }

        return $this->client->post('/v1/email/send', $body);
    }

    /**
     * Get the delivery status of a sent email.
     *
     * @return array<string, mixed>
     */
    public function getStatus(string $messageId): array
    {
        return $this->client->get("/v1/email/status/{$messageId}");
    }
}
