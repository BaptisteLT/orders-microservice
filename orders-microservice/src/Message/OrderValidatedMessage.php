<?php
namespace App\Message;

class OrderValidatedMessage
{
    public function __construct(
        public int $orderId,
    ) {}
}
