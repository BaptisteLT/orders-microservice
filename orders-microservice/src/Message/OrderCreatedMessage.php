<?php
namespace App\Message;

class OrderCreatedMessage
{
    public function __construct(
        public int $orderId,
        public string $customerUuid,
        public array $products
    ) {}
}
