<?php

namespace Tests\Support;

use App\Exceptions\MidtransUnavailableException;
use App\Services\MidtransService;

class FakeMidtransService extends MidtransService
{
    public ?object $statusResponse = null;

    public bool $statusThrows = false;

    public bool $cancelResult = true;

    public ?object $expireResponse = null;

    public ?array $lastSnapPayload = null;

    public ?string $lastCancelled = null;

    public ?string $lastExpired = null;

    public int $statusCalls = 0;

    public function createSnapToken(array $payload): string
    {
        $this->lastSnapPayload = $payload;

        return 'fake-snap-token';
    }

    public function getStatus(string $orderId): ?object
    {
        $this->statusCalls++;

        if ($this->statusThrows) {
            throw new MidtransUnavailableException('Midtrans is unavailable');
        }

        return $this->statusResponse;
    }

    public function cancel(string $orderId): bool
    {
        $this->lastCancelled = $orderId;

        return $this->cancelResult;
    }

    public function expire(string $orderId): ?object
    {
        $this->lastExpired = $orderId;

        return $this->expireResponse;
    }
}
