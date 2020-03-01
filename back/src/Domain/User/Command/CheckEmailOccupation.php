<?php

declare(strict_types=1);

namespace FeedReader\Domain\User\Command;

final class CheckEmailOccupation
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
