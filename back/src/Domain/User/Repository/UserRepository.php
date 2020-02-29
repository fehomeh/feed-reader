<?php

namespace FeedReader\Domain\User\Repository;

use FeedReader\Domain\User\User;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
interface UserRepository
{
    public function findOneByEmail(string $email): ?User;

    public function save(User $user): void;
}
