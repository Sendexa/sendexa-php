<?php

declare(strict_types=1);

namespace Sendexa\Resources;

use Sendexa\HttpClient;

class OtpResource
{
    public function __construct(private readonly HttpClient $client) {}

    /**
     * Request a new OTP to be sent to the given phone number.
     *
     * @param array<string, mixed> $expiry   e.g. ['amount' => 5, 'duration' => 'minutes']
     * @param array<string, mixed> $metadata Arbitrary key–value pairs stored with the OTP session
     * @param array<string, mixed> $options  Additional options
     * @return array<string, mixed>
     */
    public function request(
        string $phone,
        string $from,
        string $message = 'Your verification code is {code}. Valid for {amount} {duration}.',
        int $pinLength = 6,
        string $pinType = 'NUMERIC',
        array $expiry = [],
        int $maxRetries = 3,
        array $metadata = [],
        array $options = [],
    ): array {
        $body = array_merge([
            'phone'                          => $phone,
            'from'                           => $from,
            'message'                        => $message,
            'pinLength'                      => $pinLength,
            'pinType'                        => $pinType,
            'maxAmountOfValidationRetries'   => $maxRetries,
        ], $options);

        if (!empty($expiry)) {
            $body['expiry'] = $expiry;
        }
        if (!empty($metadata)) {
            $body['metadata'] = $metadata;
        }

        return $this->client->post('/v1/otp/request', $body);
    }

    /**
     * Verify the PIN a user entered against the OTP session.
     *
     * @return array<string, mixed>
     */
    public function verify(string $id, string $pin): array
    {
        return $this->client->post('/v1/otp/verify', ['id' => $id, 'pin' => $pin]);
    }

    /**
     * Resend an OTP to the same phone number (subject to cooldown).
     *
     * @return array<string, mixed>
     */
    public function resend(string $otpId): array
    {
        return $this->client->post("/v1/otp/resend/{$otpId}");
    }
}
