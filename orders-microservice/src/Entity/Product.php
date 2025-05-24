<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["customer:read:admin"])]
    private ?int $productId = null;

    #[Groups(["customer:read", "customer:read:user", "customer:read:admin"])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(["customer:read", "customer:read:user", "customer:read:admin"])]
    #[ORM\Column]
    private ?int $priceInCents = null;

    #[ORM\ManyToOne(inversedBy: 'product')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $productOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPriceInCents(): ?int
    {
        return $this->priceInCents;
    }

    public function setPriceInCents(int $priceInCents): static
    {
        $this->priceInCents = $priceInCents;

        return $this;
    }

    public function getProductOrder(): ?Order
    {
        return $this->productOrder;
    }

    public function setProductOrder(?Order $productOrder): static
    {
        $this->productOrder = $productOrder;

        return $this;
    }
}
