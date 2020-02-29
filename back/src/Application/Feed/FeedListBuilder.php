<?php

declare(strict_types=1);

namespace FeedReader\Application\Feed;

use FeedIo\Feed\Item;
use FeedIo\FeedInterface;
use FeedReader\Application\Feed\DTO\FeedItem;
use FeedReader\Application\Feed\DTO\FeedList;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class FeedListBuilder
{
    /** @var FeedInterface<Item> */
    private FeedInterface $feedInterface;
    private string $title = '';
    private string $logo = '';
    private string $url = '';
    private PopularWordsParser $wordsParser;

    /**
     * @param FeedInterface<Item> $feedInterface
     */
    public function __construct(FeedInterface $feedInterface)
    {
        $this->feedInterface = $feedInterface;
        $this->wordsParser = new PopularWordsParser();
    }

    public function withTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function withLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function withUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function build(): FeedList
    {
        $feedList = new FeedList();
        $feedList->title = $this->title;
        $feedList->logo = $this->logo;
        $feedList->url = $this->url;
        $items = [];
        foreach ($this->feedInterface as $feedItem) {
            assert($feedItem instanceof Item);
            $item = new FeedItem();
            $item->title = $feedItem->getTitle() ?? '';
            $item->link = $feedItem->getLink() ?? '';
            $item->summary = $feedItem->getDescription() ?? '';
            $item->lastModified = $feedItem->getLastModified();
            $item->authorName = null !== $feedItem->getAuthor() ? (string) $feedItem->getAuthor()->getName() : '';
            $item->authorEmail = null !== $feedItem->getAuthor() ? (string) $feedItem->getAuthor()->getEmail() : '';
            $item->authorUri = null !== $feedItem->getAuthor() ? (string) $feedItem->getAuthor()->getUri() : '';
            $items[] = $item;
        }
        $feedList->items = $items;
        $feedList->mostPopularWords = $this->wordsParser->parse($items);

        return $feedList;
    }
}
