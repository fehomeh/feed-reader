<?php

declare(strict_types=1);

namespace FeedReader\Domain\User\Command;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class RegisterUser
{
    private string $email;

    private string $password;

    private string $passwordRepeat;

    /**
     * @param string $email
     * @param string $password
     * @param string $passwordRepeat
     */
    public function __construct(string $email, string $password, string $passwordRepeat)
    {
        $this->email = $email;
        $this->password = $password;
        $this->passwordRepeat = $passwordRepeat;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPasswordRepeat(): string
    {
        return $this->passwordRepeat;
    }

    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->password !== $this->passwordRepeat) {
            $context->buildViolation('Passwords do not match')
                ->atPath('password')
                ->addViolation();
        }
        if (!preg_match('/^(?=.*[\w])(?=.*[\d])[\w0-9]{6,}$/', $this->password)) {
            $context->buildViolation('Password must contain letter, digit and be at least 6 symbols')
                ->atPath('password')
                ->addViolation();
        }
    }
}
