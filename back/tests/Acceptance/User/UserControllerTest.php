<?php

declare(strict_types=1);

namespace FeedReader\Tests\Acceptance\User;

use FeedReader\Domain\User\Repository\UserRepository;
use FeedReader\Domain\User\User;
use FeedReader\Tests\Acceptance\AcceptanceTestCase;
use Symfony\Component\HttpFoundation\Response;

final class UserControllerTest extends AcceptanceTestCase
{
    private const USER_TEST_PASSWORD = 'AXop12!)';

    public function testAuthenticationSuccessful(): void
    {
        $this->createUser();
        $client = $this->request(
            'POST',
            'users/login',
            [
                'username' => AcceptanceTestCase::TEST_USER_EMAIL,
                'password' => self::TEST_USER_PASSWORD,
            ]
        );

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        self::assertTrue($response['success']);
        self::assertSame(AcceptanceTestCase::TEST_USER_EMAIL, $response['username']);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testAuthenticationFailed(): void
    {
        $this->createUser();
        $client = $this->request(
            'POST',
            'users/login',
            [
                'username' => AcceptanceTestCase::TEST_USER_EMAIL . 'WRONG!!!',
                'password' => self::TEST_USER_PASSWORD,
            ]
        );

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        self::assertSame('Invalid credentials.', $response['error']);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    public function testEmailIsFree(): void
    {
        $this->createUser();
        $client = $this->request('GET', 'users/email/test@me.com');

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        self::assertTrue($response['is_free']);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testEmailIsOccupied(): void
    {
        $this->createUser();
        $client = $this->request('GET', 'users/email/' . AcceptanceTestCase::TEST_USER_EMAIL);

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        self::assertFalse($response['is_free']);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testRegisteredSuccessfully(): void
    {
        $client = $this->request(
            'POST',
            'users',
            [
                'email' => AcceptanceTestCase::TEST_USER_EMAIL,
                'password' => self::USER_TEST_PASSWORD,
                'repeat' => self::USER_TEST_PASSWORD,
            ]
        );

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        $repository = self::$container->get(UserRepository::class);
        $user = $repository->findOneByEmail(AcceptanceTestCase::TEST_USER_EMAIL);
        self::assertInstanceOf(User::class, $user);
        self::assertSame(AcceptanceTestCase::TEST_USER_EMAIL, $user->getEmail());
        self::assertTrue($response['success']);
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    public function testCantRegisterWithWrongValues(): void
    {
        $client = $this->request(
            'POST',
            'users',
            [
                'email' => '',
                'password' => '1',
                'repeat' => '1b',
            ]
        );

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        self::assertFalse($response['success']);
        self::assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        self::assertSame(
            $response['error'],
            [
                'password' => [
                    'Passwords do not match',
                    'Password must contain letter, digit and be at least 6 symbols',
                ],
                'email' => ['This value should not be blank.'],
            ]
        );
    }

    public function testCantRegisterWithFakeEmailAndShortPassword(): void
    {
        $client = $this->request(
            'POST',
            'users',
            [
                'email' => 'asd@',
                'password' => '1111',
                'repeat' => '1111',
            ]
        );

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        self::assertFalse($response['success']);
        self::assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        self::assertSame(
            $response['error'],
            [
                'password' => ['Password must contain letter, digit and be at least 6 symbols'],
                'email' => ['This value is not a valid email address.'],
            ]
        );
    }

    public function testCantRegisterWithExistingEmail(): void
    {
        $this->createUser();
        $client = $this->request(
            'POST',
            'users',
            [
                'email' => AcceptanceTestCase::TEST_USER_EMAIL,
                'password' => self::USER_TEST_PASSWORD,
                'repeat' => self::USER_TEST_PASSWORD,
            ]
        );

        $response = $this->safeJsonDecode($client->getResponse()->getContent());
        self::assertFalse($response['success']);
        self::assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        self::assertSame(
            $response['error'],
            'User with such email exists: admin@test.com',
        );
    }
}
