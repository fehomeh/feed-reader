<?php

declare(strict_types=1);

namespace FeedReader\Application\Feed;

use FeedReader\Application\Feed\DTO\FeedItem;

final class PopularWordsParser
{
    private const TOP_50_POPULAR = [
        'the',
        'to',
        'of',
        'and',
        'a',
        'in',
        'that',
        'have',
        'I',
        'it',
        'for',
        'not',
        'on',
        'with',
        'he',
        'as',
        'you',
        'do',
        'at',
        'this',
        'but',
        'his',
        'by',
        'from',
        'they',
        'we',
        'say',
        'her',
        'she',
        'or',
        'an',
        'will',
        'my',
        'one',
        'all',
        'would',
        'there',
        'their',
        'what',
        'so',
        'up',
        'out',
        'if',
        'about',
        'who',
        'get',
        'which',
        'go',
        'me',
        'when',
        'make',
    ];
    private const MINIMUM_WORD_LENGTH = 2;
    private const STR_WORD_RETURN_WORDS_FORMAT = 1;
    private const NUMBER_OF_POPULAR_WORDS = 10;

    /**
     * @param FeedItem[] $items
     *
     * @return array<string, int>
     */
    public function parse(array $items): array
    {
        $result = [];
        foreach ($items as $item) {
            $words = $this->extractWordsFromTitleAndSummary($item);
            $result = $this->sumWords($result, $words);
        }
        arsort($result);

        return array_slice($result, 0, self::NUMBER_OF_POPULAR_WORDS);
    }

    /**
     * @param FeedItem $item
     *
     * @return array<string, int>
     */
    private function extractWordsFromTitleAndSummary(FeedItem $item): array
    {
        return $this->sumWords($this->parseWordsFromString($item->title), $this->parseWordsFromString($item->summary));
    }

    /**
     * @param string $text
     *
     * @return array<string, int>
     */
    public function parseWordsFromString(string $text): array
    {
        $lowerWords = array_map('strtolower', str_word_count(strip_tags($text), self::STR_WORD_RETURN_WORDS_FORMAT));
        $words = $this->removeShortWords($lowerWords);
        $words = $this->removeFrequentWords($words);

        return array_count_values($words);
    }

    /**
     * @param string[] $words
     *
     * @return string[]
     */
    private function removeShortWords(array $words): array
    {
        return array_filter(
            $words,
            static function (string $word) {
                return strlen($word) >= self::MINIMUM_WORD_LENGTH;
            }
        );
    }

    /**
     * @param string[] $words
     *
     * @return string[]
     */
    private function removeFrequentWords(array $words): array
    {
        return array_diff($words, self::TOP_50_POPULAR);
    }

    /**
     * @param array<string, int> $words1
     * @param array<string, int> $words2
     *
     * @return array<string, int>
     */
    private function sumWords(array $words1, array $words2): array
    {
        foreach ($words2 as $word => $count) {
            if (!array_key_exists($word, $words1)) {
                $words1[$word] = 0;
            }
            $words1[$word] += $count;
        }

        return $words1;
    }
}
