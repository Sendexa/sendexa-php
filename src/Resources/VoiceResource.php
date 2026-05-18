<?php

declare(strict_types=1);

namespace Sendexa\Resources;

use Sendexa\HttpClient;

class VoiceResource
{
    public function __construct(private readonly HttpClient $client) {}

    /**
     * Initiate a programmatic outbound call.
     *
     * @param array<string, mixed> $options  twiml, url, record, statusCallbackUrl, machineDetection
     * @return array<string, mixed>
     */
    public function call(string $to, string $from, array $options = []): array
    {
        return $this->client->post('/v1/voice/call', array_merge([
            'to'   => $to,
            'from' => $from,
        ], $options));
    }

    /**
     * Make an outbound call that reads text aloud (text-to-speech).
     *
     * @param array<string, mixed> $options  loop, language, voice
     * @return array<string, mixed>
     */
    public function tts(
        string $to,
        string $from,
        string $text,
        string $language = 'en-US',
        string $voice = 'female',
        int $loop = 1,
        array $options = [],
    ): array {
        return $this->client->post('/v1/voice/tts', array_merge([
            'to'       => $to,
            'from'     => $from,
            'text'     => $text,
            'language' => $language,
            'voice'    => $voice,
            'loop'     => $loop,
        ], $options));
    }

    /**
     * Make an outbound call that plays an audio file.
     *
     * @param array<string, mixed> $options  loop
     * @return array<string, mixed>
     */
    public function play(
        string $to,
        string $from,
        string $audioUrl,
        int $loop = 1,
        array $options = [],
    ): array {
        return $this->client->post('/v1/voice/play', array_merge([
            'to'       => $to,
            'from'     => $from,
            'audioUrl' => $audioUrl,
            'loop'     => $loop,
        ], $options));
    }

    /**
     * Get the status and details of a call.
     *
     * @return array<string, mixed>
     */
    public function getStatus(string $callId): array
    {
        return $this->client->get("/v1/voice/status/{$callId}");
    }
}
