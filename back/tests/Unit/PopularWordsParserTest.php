<?php

declare(strict_types=1);

namespace FeedReader\Tests\Unit;

use FeedReader\Application\Feed\DTO\FeedItem;
use FeedReader\Application\Feed\PopularWordsParser;
use PHPUnit\Framework\TestCase;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class PopularWordsParserTest extends TestCase
{
    public function testParseLorumParagraph(): void
    {
        $parser = new PopularWordsParser();

        $actualResult = $parser->parse($this->createItemFromText($this->readTextFile('lipsum_text.txt')));

        self::assertSame($this->expectedLipsumResult(), $actualResult);
    }

    public function testAllWordsAreTheMostPopular(): void
    {
        $parser = new PopularWordsParser();

        $actualResult = $parser->parse($this->createItemFromText($this->readTextFile('all_popular_words.txt')));

        self::assertSame([], $actualResult);
    }

    private function readTextFile(string $filename): string
    {
        return file_get_contents(__DIR__ . '/../resources/' . $filename);
    }

    /**
     * @param string $readTextFile
     *
     * @return FeedItem[]
     */
    private function createItemFromText(string $readTextFile): array
    {
        $item = new FeedItem();
        $item->summary = $readTextFile;
        $item->title = '';

        return [$item];
    }

    /**
     * @return int[]
     */
    private function expectedLipsumResult(): array
    {
        return [
            'donec' => 4,
            'et' => 4,
            'lorem' => 2,
            'lectus' => 2,
            'facilisis' => 2,
            'non' => 2,
            'ipsum' => 2,
            'erat' => 2,
            'id' => 2,
            'condimentum' => 2,
        ];
    }
}
