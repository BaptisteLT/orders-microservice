<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\OrderInputDto;
use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\OrderCreatedMessage;

class OrderProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private MessageBusInterface $bus,
        ) {}



    /**
     * @param OrderInputDto $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Order
    {
        $user = $this->security->getUser();

        $order = new Order();
        $order->setCreatedAt($data->createdAt);
    
        $customer = new Customer();
        $customer->setFirstName($data->customer->firstName)
                 ->setLastName($data->customer->lastName)
                 ->setPostalCode($data->customer->postalCode)
                 ->setCity($data->customer->city)
                 ->setCustomerUuid($user->getUuid());
    
        $order->setCustomer($customer);
    
        $productPayload = [];

        foreach ($data->product as $p) {
            $product = new Product();
            $product->setProductId($p->productId)
                    ->setName($p->name)
                    ->setPriceInCents($p->priceInCents)
                    ->setQuantity($p->quantity);
            $order->addProduct($product);
            $this->em->persist($product);

            $productPayload[] = [
                'productId' => $p->productId,
                'name' => $p->name,
                'priceInCents' => $p->priceInCents,
                'quantity' => $p->quantity,
            ];
        }
    
        $this->em->persist($order);
        $this->em->flush();
    
        // ensure the returned order has an ID
        $managedOrder = $this->em->getRepository(Order::class)->find($order->getId());

        //Message envoyé sur le topic orders configuré dans messenger.yaml
        $this->bus->dispatch(new OrderCreatedMessage(
            orderId: $order->getId(),
            customerUuid: $user->getUuid(),
            products: $productPayload
        ));

        return $managedOrder;
    }
}
