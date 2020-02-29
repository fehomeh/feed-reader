<?php

declare(strict_types=1);

namespace FeedReader\Tests\Util;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class GuzzleClientFactory
{
    private static $handler;

    public static function create(): Client
    {
        $handlerStack = HandlerStack::create(self::getHandler());

        return new Client(['handler' => $handlerStack]);
    }

    public static function addResponse(ResponseInterface $response): void
    {
        self::getHandler()->append($response);
    }

    private static function getHandler(): MockHandler
    {
        if (null === self::$handler) {
            self::$handler = new MockHandler();
        }

        return self::$handler;
    }
}
