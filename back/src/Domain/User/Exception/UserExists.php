<?php

declare(strict_types=1);

namespace FeedReader\Domain\User\Exception;

use LogicException;

final class UserExists extends LogicException
{
    public static function create(string $email): self
    {
        return new self(sprintf('User with such email exists: %s', $email));
    }
}
