<?php

declare(strict_types=1);

namespace FeedReader\Domain\User\CommandHandler;

use FeedReader\Domain\User\Command\CheckEmailOccupation;
use FeedReader\Domain\User\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CheckEmailOccupationHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(CheckEmailOccupation $emailOccupation): bool
    {
        return null === $this->userRepository->findOneByEmail($emailOccupation->getEmail());
    }
}
