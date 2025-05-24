<?php
namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

class OrderCustomFilterExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private Security $security) {}

    public function applyToCollection(QueryBuilder $qb, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if (
            !$operation instanceof GetCollection || 
            $operation->getUriTemplate() !== '/my-orders' || 
            $resourceClass !== \App\Entity\Order::class || 
            $this->security->isGranted('ROLE_ADMIN')
        ) {
            return;
        }

        $rootAlias = $qb->getRootAliases()[0];

        $customerAlias = $queryNameGenerator->generateJoinAlias('customer');
        $qb->join("$rootAlias.customer", $customerAlias);

        $qb->andWhere("$customerAlias.customerUuid = :current_user")
        ->setParameter(
            'current_user', 
            Uuid::fromString($this->security->getUser()->getUuid())->toBinary(), 
            'uuid'
        );
    }
}