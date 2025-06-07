<?php
namespace App\MessageHandler;

use App\Message\OrderValidatedMessage;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class OrderValidatedMessageHandler
{
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}

    public function __invoke(OrderValidatedMessage $message)
    {
        $order = $this->orderRepository->find($message->orderId);

        if (!$order) {
            $this->logger->error('Order not found for cancellation', [
                'orderId' => $message->orderId,
            ]);
            return;
        }

        $order->setStatus('validated');
        $this->em->persist($order);
        $this->em->flush();

        $this->logger->info('Order validated successfully', [
            'orderId' => $message->orderId,
        ]);
    }
}
