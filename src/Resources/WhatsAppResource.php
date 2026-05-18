<?php

declare(strict_types=1);

namespace Sendexa\Resources;

use Sendexa\HttpClient;

class WhatsAppResource
{
    public function __construct(private readonly HttpClient $client) {}

    /**
     * Send a raw WhatsApp message payload.
     *
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function send(array $body): array
    {
        return $this->client->post('/v1/whatsapp/send', $body);
    }

    /**
     * Send a plain-text WhatsApp message.
     *
     * @return array<string, mixed>
     */
    public function sendText(string $to, string $text, bool $previewUrl = false): array
    {
        return $this->send([
            'to'   => $to,
            'type' => 'text',
            'text' => ['body' => $text, 'preview_url' => $previewUrl],
        ]);
    }

    /**
     * Send an image message with an optional caption.
     *
     * @return array<string, mixed>
     */
    public function sendImage(string $to, string $url, ?string $caption = null): array
    {
        $image = ['link' => $url];
        if ($caption !== null) {
            $image['caption'] = $caption;
        }
        return $this->send(['to' => $to, 'type' => 'image', 'image' => $image]);
    }

    /**
     * Send a document or file attachment.
     *
     * @return array<string, mixed>
     */
    public function sendDocument(
        string $to,
        string $url,
        ?string $caption = null,
        ?string $filename = null,
    ): array {
        $doc = ['link' => $url];
        if ($caption !== null) {
            $doc['caption'] = $caption;
        }
        if ($filename !== null) {
            $doc['filename'] = $filename;
        }
        return $this->send(['to' => $to, 'type' => 'document', 'document' => $doc]);
    }

    /**
     * Send an interactive message (buttons, list, or CTA URL).
     *
     * @param array<string, mixed> $interactive
     * @return array<string, mixed>
     */
    public function sendInteractive(string $to, array $interactive): array
    {
        return $this->send(['to' => $to, 'type' => 'interactive', 'interactive' => $interactive]);
    }

    /**
     * Send an approved WhatsApp Business template message.
     *
     * @param array<string, mixed> $template
     * @return array<string, mixed>
     */
    public function sendTemplate(string $to, array $template): array
    {
        return $this->send(['to' => $to, 'type' => 'template', 'template' => $template]);
    }

    /**
     * Get the delivery status of a WhatsApp message.
     *
     * @return array<string, mixed>
     */
    public function getStatus(string $messageId): array
    {
        return $this->client->get("/v1/whatsapp/status/{$messageId}");
    }

    /**
     * Resend a failed WhatsApp message.
     *
     * @return array<string, mixed>
     */
    public function resend(string $messageId): array
    {
        return $this->client->post("/v1/whatsapp/resend/{$messageId}");
    }
}
