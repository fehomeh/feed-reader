<?php

declare(strict_types=1);

namespace FeedReader\Application\Feed\DTO;

use DateTime;

final class FeedItem
{
    public string $title;
    public string $link;
    public string $summary;
    /**
     * @var DateTime|null
     */
    public ?DateTime $lastModified;
    public string $authorName;
    public string $authorEmail;
    public string $authorUri;
}
