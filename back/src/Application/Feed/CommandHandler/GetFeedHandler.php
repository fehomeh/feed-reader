<?php

declare(strict_types=1);

namespace FeedReader\Application\Feed\CommandHandler;

use FeedIo\FeedIo;
use FeedReader\Application\Feed\Command\GetFeed;
use FeedReader\Application\Feed\DTO\FeedList;
use FeedReader\Application\Feed\FeedListBuilder;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class GetFeedHandler implements MessageHandlerInterface
{
    private FeedIo $feedReader;
    private string $feedUrl;

    /**
     * @param FeedIo $feedReader
     * @param string $feedUrl
     */
    public function __construct(FeedIo $feedReader, string $feedUrl)
    {
        $this->feedReader = $feedReader;
        $this->feedUrl = $feedUrl;
    }

    public function __invoke(GetFeed $getFeed): FeedList
    {
        $feed = $this->feedReader->read($this->feedUrl);
        $builder = new FeedListBuilder($feed->getFeed());
        $itemsList = $builder->withLogo($feed->getFeed()->getLogo() ?? '')
            ->withTitle($feed->getFeed()->getTitle() ?? '')
            ->withUrl($feed->getFeed()->getUrl() ?? '')
            ->build();

        return $itemsList;
    }
}
