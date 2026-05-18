<?php

declare(strict_types=1);

namespace Sendexa;

use Sendexa\Resources\{
    SmsResource,
    OtpResource,
    WhatsAppResource,
    EmailResource,
    VoiceResource,
    WebhooksResource,
};

/**
 * The official Sendexa PHP SDK client.
 *
 * Create one instance per application and reuse it — the client is
 * stateless and safe to share across requests.
 *
 * Authentication — provide one of:
 *  - apiKey + apiSecret  (credentials from your dashboard)
 *  - token               (pre-computed Base64 string: base64("key:secret"))
 *
 * @example
 * use Sendexa\Sendexa;
 *
 * $client = new Sendexa(
 *     apiKey:    $_ENV['SENDEXA_API_KEY'],
 *     apiSecret: $_ENV['SENDEXA_API_SECRET'],
 * );
 *
 * $client->sms->send('0244123456', 'MyBrand', 'Hello!');
 */
class Sendexa
{
    public readonly SmsResource       $sms;
    public readonly OtpResource       $otp;
    public readonly WhatsAppResource  $whatsapp;
    public readonly EmailResource     $email;
    public readonly VoiceResource     $voice;
    public readonly WebhooksResource  $webhooks;

    public function __construct(
        ?string $apiKey    = null,
        ?string $apiSecret = null,
        ?string $token     = null,
        string  $baseUrl   = 'https://api.sendexa.co',
        float   $timeout   = 30.0,
    ) {
        $http = new HttpClient(
            apiKey:    $apiKey,
            apiSecret: $apiSecret,
            token:     $token,
            baseUrl:   $baseUrl,
            timeout:   $timeout,
        );

        $this->sms      = new SmsResource($http);
        $this->otp      = new OtpResource($http);
        $this->whatsapp = new WhatsAppResource($http);
        $this->email    = new EmailResource($http);
        $this->voice    = new VoiceResource($http);
        $this->webhooks = new WebhooksResource();
    }
}
