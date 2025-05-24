<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductInputDto
{
    #[Assert\NotNull]
    public int $productId;

    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotNull]
    public int $priceInCents;
}
