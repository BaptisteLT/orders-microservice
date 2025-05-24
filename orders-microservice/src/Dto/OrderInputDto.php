<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class OrderInputDto
{
    #[Assert\NotNull]
    public ?\DateTimeImmutable $createdAt = null;

    #[Assert\Valid]
    public CustomerInputDto $customer;

    /**
     * @var ProductInputDto[]
     */
    #[Assert\Valid]
    public array $product = [];
}