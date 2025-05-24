<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CustomerInputDto
{
    #[Assert\NotBlank]
    public string $firstName;

    #[Assert\NotBlank]
    public string $lastName;

    #[Assert\NotBlank]
    public string $postalCode;

    #[Assert\NotBlank]
    public string $city;

}
