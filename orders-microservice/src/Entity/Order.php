<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\OrderInputDto;
use App\Repository\OrderRepository;
use App\State\OrderProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;



#[Put(security: "is_granted('ROLE_ADMIN')")]
#[Patch(security: "is_granted('ROLE_ADMIN')")]
#[GetCollection(
    normalizationContext: ['groups' => ['customer:read:admin']],
    security: "is_granted('ROLE_ADMIN')"
    )
]
#[Post(
        input: OrderInputDto::class,
        processor: OrderProcessor::class,
        security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')"
    )
]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
#[ApiResource(
    normalizationContext: ['groups' => ['customer:read']],
    denormalizationContext: ['groups' => ['customer:write']],
    operations: [
        new Get(
            uriTemplate: '/admin/orders/{id}',
            normalizationContext: ['groups' => ['customer:read:admin']],
            security: "is_granted('ROLE_ADMIN')",
            requirements: ['id' => '\d+']
        ),
        new Get(
            normalizationContext: ['groups' => ['customer:read']],
            security: "is_granted('ROLE_ADMIN') or (object.getCustomer() and object.getCustomer().getCustomerUuid() == user.getId())"
        ),
        //Get orders a user owns or orders containing only items a web_shopper owns
        new GetCollection(
            uriTemplate: '/my-orders',
            normalizationContext: ['groups' => ['customer:read']],
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER') or is_granted('ROLE_WEB_SHOPPER')"
        ),
        //Pour ROLE_WEB_SHOPPER
        new GetCollection(
            uriTemplate: '/my-customers-orders',
            normalizationContext: ['groups' => ["webshopper:read"]],
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_WEB_SHOPPER')"
        ),
        new Get(
            uriTemplate: '/my-customers-orders/{id}',
            normalizationContext: ['groups' => ['webshopper:read']],
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_WEB_SHOPPER')"
        ),
    ]
)]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(["webshopper:read", "customer:read", "customer:read:user", "customer:read:admin", "customer:write"])]
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Product>
     */
    #[Groups(["webshopper:read", "customer:read", "customer:read:user", "customer:read:admin"])]
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'productOrder', orphanRemoval: true)]
    private Collection $product;

    #[Groups(["webshopper:read", "customer:read", "customer:read:user", "customer:read:admin"])]
    #[ORM\OneToOne(inversedBy: 'customerOrder', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;


    public function __construct()
    {
        $this->product = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCustomerId(): ?int{
        return $this->customer?->getId();
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
            $product->setProductOrder($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getProductOrder() === $this) {
                $product->setProductOrder(null);
            }
        }

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

}
