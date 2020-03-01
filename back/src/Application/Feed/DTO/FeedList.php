<?php

declare(strict_types=1);

namespace FeedReader\Application\Feed\DTO;

final class FeedList
{
    /** @var array<string, int> */
    public array $mostPopularWords;
    /** @var array<FeedItem> */
    public array $items;

    public string $title;

    public string $logo;

    public string $url;
}
