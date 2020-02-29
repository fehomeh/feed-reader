<?php

declare(strict_types=1);

namespace FeedReader\Domain\User\CommandHandler;

use FeedReader\Domain\User\Command\RegisterUser;
use FeedReader\Domain\User\Exception\UserExists;
use FeedReader\Domain\User\Repository\UserRepository;
use FeedReader\Domain\User\User;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class RegisterUserHandler implements MessageHandlerInterface
{
    private UserRepository $repository;
    private UserPasswordEncoderInterface $encoder;

    /**
     * @param UserRepository $repository
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserRepository $repository, UserPasswordEncoderInterface $encoder)
    {
        $this->repository = $repository;
        $this->encoder = $encoder;
    }

    public function __invoke(RegisterUser $userCommand): void
    {
        if (null !== $this->repository->findOneByEmail($userCommand->getEmail())) {
            throw UserExists::create($userCommand->getEmail());
        }

        $user = new User($userCommand->getEmail(), []);
        $user->setPassword($this->encoder->encodePassword($user, $userCommand->getPassword()));

        $this->repository->save($user);
    }
}
