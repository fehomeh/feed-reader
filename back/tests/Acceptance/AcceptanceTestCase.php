<?php

declare(strict_types=1);

namespace FeedReader\Tests\Acceptance;

use FeedReader\Domain\User\Repository\UserRepository;
use FeedReader\Domain\User\User;
use FeedReader\Tests\Util\GuzzleClientFactory;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class AcceptanceTestCase extends WebTestCase
{
    public const TEST_USER_PASSWORD = 'test_123';
    public const TEST_USER_EMAIL = 'admin@test.com';
    /**
     * @var KernelBrowser
     */
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function request(
        string $method,
        string $endpoint,
        array $data = []
    ): KernelBrowser {

        $server = [
            'ACCEPT' => ['application/json'],
            'CONTENT_TYPE' => 'application/json',
        ];
        $body = null;
        if (in_array($method, ['POST', 'PUT'], true)) {
            $body = json_encode($data, JSON_THROW_ON_ERROR);
        }

        $this->client->request($method, '/api/v1.0/' . $endpoint, [], [], $server, $body);

        return $this->client;
    }

    protected function safeJsonDecode(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string> $roles
     *
     * @return User
     */
    protected function createUser(array $roles = []): User
    {
        self::bootKernel();
        $repository = self::$container->get(UserRepository::class);
        $encoder = self::$container->get(UserPasswordEncoderInterface::class);

        $user = new User(self::TEST_USER_EMAIL, $roles);
        $user->setPassword($encoder->encodePassword($user, self::TEST_USER_PASSWORD));

        $repository->save($user);

        return $user;
    }

    protected function logIn(User $user, string $role): void
    {
        $session = self::$container->get('session');

        $firewallName = 'main';
        $firewallContext = 'main';
        $token = new UsernamePasswordToken($user, self::TEST_USER_PASSWORD, $firewallName, [$role]);
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function addGuzzleResponse(ResponseInterface $response): void
    {
        GuzzleClientFactory::addResponse($response);
    }
}
