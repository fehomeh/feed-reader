<?php

namespace FeedReader\Domain\User\Repository;

use FeedReader\Domain\User\User;

interface UserRepository
{
    public function findOneByEmail(string $email): ?User;

    public function save(User $user): void;
}
