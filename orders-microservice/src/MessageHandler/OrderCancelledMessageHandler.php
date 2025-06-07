<?php
namespace App\MessageHandler;

use App\Message\OrderCancelledMessage;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class OrderCancelledMessageHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}

    public function __invoke(OrderCancelledMessage $message)
    {
        $this->logger->info('Received OrderCancelledMessage');
    
        $order = $this->orderRepository->find($message->orderId);

        if (!$order) {
            $this->logger->error('Order not found for cancellation', [
                'orderId' => $message->orderId,
            ]);
            return;
        }

        $order->setStatus('cancelled');
        $this->em->persist($order);
        $this->em->flush();

        $this->logger->info('Order cancelled successfully', [
            'orderId' => $message->orderId,
        ]);
    }
}
