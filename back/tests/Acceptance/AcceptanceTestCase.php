<?php

declare(strict_types=1);

namespace FeedReader\Tests\Acceptance;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AcceptanceTestCase extends WebTestCase
{
    protected function request(
        string $method,
        string $endpoint,
        array $data = []
    ): KernelBrowser {
        $client  = static::createClient();
        $options = [
            'headers' => [
                'accept' => ['application/json'],
                'content-type' => 'application/json',
            ],
        ];
        if (in_array($method, ['POST', 'PUT'], true)) {
            $options['body'] = ($data === [] ? '{}' : json_encode($data, JSON_THROW_ON_ERROR));
        }

        $client->request($method, '/api/' . $endpoint, $options);

        return $client;
    }

    protected function createUser(string $id) : User
    {
        self::bootKernel();
        $repository = self::$container->get(UserRepository::class);
        assert($repository instanceof UserRepository);
        $user = User::create(
            new UserId(Uuid::fromString($id)),
            new CreateUser(
                'abc',
                new Money(0, new Currency('USD')),
            ),
            );
        $repository->save($user);

        return $user;
    }
}
