<?php
namespace App\Serializer;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

class WebShopperOrderGetFilterProductsNormalizer implements NormalizerInterface
{
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer,
        private readonly Security $security,
        private UrlGeneratorInterface $router,
    ) {
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Order) {
            return false;
        }

        $operation = $context['root_operation'] ?? $context['operation'] ?? null;
        return $operation instanceof Get && 
               $operation->getUriTemplate() === '/my-customers-orders/{id}';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Order::class => true,
        ];
    }

    public function normalize($object, ?string $format = null, array $context = []): array
    {
     
        $data = $this->normalizer->normalize($object, $format, $context);

        $user = $this->security->getUser();
        if (!$user || !isset($data['product'])) {
            return $data;
        }

        $userUuid = $user->getUuid();

        
        // Filter the product members to only include those with matching customerUuid
        if (isset($data['product']['member'])) {
    
            $data['product']['member'] = array_values(array_filter(
                $data['product']['member'],
                function ($product) use ($userUuid) {
                    return ($product['customerUuid'] ?? null) === $userUuid;
                }
            ));
            
            // Update the totalItems count to reflect the filtered count
            $data['product']['totalItems'] = count($data['product']['member']);
        }


        return $data;
    }
}