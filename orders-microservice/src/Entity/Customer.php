<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(["customer:read:user", "customer:read:admin"])]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Groups(["customer:read:user", "customer:read:admin"])]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[Groups(["customer:read:user", "customer:read:admin"])]
    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[Groups(["customer:read:user", "customer:read:admin"])]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[Groups(["customer:read:admin"])]
    #[ORM\Column(nullable: true)]
    private ?int $customerId = null;

    #[ORM\OneToOne(mappedBy: 'customer', cascade: ['persist', 'remove'])]
    private ?Order $customerOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): static
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getCustomerOrder(): ?Order
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(Order $customerOrder): static
    {
        // set the owning side of the relation if necessary
        if ($customerOrder->getCustomer() !== $this) {
            $customerOrder->setCustomer($this);
        }

        $this->customerOrder = $customerOrder;

        return $this;
    }
}
