<?php
namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUser as BaseJWTUser;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTUser extends BaseJWTUser implements UserInterface
{
    private string $uuid;

    public function __construct(string $username, array $roles = [], ?string $uuid = null)
    {
        parent::__construct($username, $roles);
        $this->uuid = $uuid;
    }

    public function getId(): string
    {
        return $this->uuid;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public static function createFromPayload($username, array $payload): self
    {
        return new static(
            $username,
            $payload['roles'] ?? [],
            $payload['uuid'] ?? null
        );
    }
}