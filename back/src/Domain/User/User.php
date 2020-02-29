<?php

namespace FeedReader\Domain\User;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
class User implements UserInterface
{
    private const DEFAULT_ROLE_USER = 'ROLE_USER';

    private UuidInterface $id;
    private string $email;
    /** @var array<string> */
    private array $roles;
    private string $password;
    private string $salt;

    /**
     * @param string $email
     * @param array<string> $roles
     */
    public function __construct(string $email, array $roles = [])
    {
        $this->id = Uuid::uuid4();
        $this->email = $email;
        $this->roles = count($roles) > 0 ? $roles : [self::DEFAULT_ROLE_USER];
        $this->salt = sha1((string) time());
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @return array<string>
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
    }
}
