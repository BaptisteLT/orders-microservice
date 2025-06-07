<?php
namespace App\Message;

class OrderCancelledMessage
{
    public function __construct(
        public int $orderId,
    ) {}
}
