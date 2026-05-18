<?php

declare(strict_types=1);

namespace Sendexa;

class SendexaException extends \RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly string $code,
        public readonly ?string $requestId = null,
        public readonly mixed $raw = null,
    ) {
        parent::__construct($message, $status);
    }

    public function __toString(): string
    {
        return sprintf(
            'SendexaException(status=%d, code=%s, message=%s)',
            $this->status,
            $this->code,
            $this->getMessage(),
        );
    }
}
