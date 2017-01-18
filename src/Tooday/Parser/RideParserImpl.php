<?php

namespace Tooday\Parser;

use Tooday\Exceptions\ParserException;

/**
 * Implementation for Slovak ride-parser.
 *
 * @package Tooday\Parser
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
class RideParserImpl implements RideParser
{
    // *** 'Where' parsing
    /**
     * Regular expression for place parsing.
     * Parsing all kinds of 'arrow' style:
     *   Ex. ... City1 ~> City2 ...
     *       ... City1 - City2 ...
     *
     * Note: In WHERE_ARROWS if one arrow is sub-arrow of another
     *       it must be listed before to match first.
     *       (ex. -->|-> or >>>|>>|>)
     */
    const WHERE_ARROWS = '(?:-->|->|-|=>|~>|>>>|>>|>)';
    const WHERE_REGEX_ARROWS = '/((\w+)\s*' . self::WHERE_ARROWS . '{1}\s*)+(\w+)/';

    /**
     * Regular expression for place parsing.
     * Parsing 'from-to' style:
     *   Ex. ... from City1 to City2 ...
     */
    const WHERE_REGEX_FROM_TO = '/z\s+(?<from>\w+)\s+do\s+(?<to>\w+)/';

    public function where($post)
    {
        $match = [];

        if (preg_match(self::WHERE_REGEX_ARROWS, $post, $match)) {
            return $this->whereArrow($match[0]);
        } else if (preg_match(self::WHERE_REGEX_FROM_TO, $post, $where)) {
            return $where;
        }

        return $where;
    }

    private function whereArrow($match)
    {
        $where = [];
        $cities = array_map('trim', preg_split('/' . self::WHERE_ARROWS . '/', $match));

        $where['from'] = array_shift($cities);
        $where['to'] = array_pop($cities);
        if ($cities) {
            $where['through'] = $cities;
        }

        return $where;
    }

    // *** 'When' parsing
    /**
     * List of commonly used 'time' adverbs.
     */
    const ADVERBS = [
        'dnes',
        'zajtra',
        'pozajtra',
        'popozajtra',
    ];

    /**
     * List of week's days with their appropriate declension.
     */
    const DAYS = [
        'nedela'   => 0,
        'nedeľa'   => 0,
        'pondelok' => 1,
        'utorok'   => 2,
        'stredu'   => 3,
        'streda'   => 3,
        'stvrtok'  => 4,
        'štvrtok'  => 4,
        'piatok'   => 5,
        'sobotu'   => 6,
        'sobota'   => 6,
        'nedelu'   => 7,
    ];

    public function when($string)
    {
        $when = array();
        $fromDay = "";
        $fromAdverb = "";

        $day = $this->containsOneOf($string, array_keys(self::DAYS));
        $adverb = $this->containsOneOf($string, self::ADVERBS);
        if ($adverb) {
            $fromAdverb = $this->getDateFromAdverb($adverb);
        }
        if ($day) {
            $fromDay = $this->getDateFromDay($day);
        }

        if ($fromAdverb && $fromDay && $fromAdverb !== $fromDay) {
            throw new ParserException('Dates are not consistent');
        }

        $when['date'] = $fromAdverb ?: $fromDay;

        return $when;
    }

    private function getDateFromAdverb($adverb)
    {
        $add = array_search($adverb, self::ADVERBS);

        return date('Y-m-d', strtotime("+ $add days"));
    }

    private function getDateFromDay($day)
    {
        $currentDayNumber = date('w');
        $dayNumber = self::DAYS[$day];
        $add = $dayNumber - $currentDayNumber;
        if ($add < 0) {
            return null;
        }

        return date('Y-m-d', strtotime("+ $add days"));
    }

    // *** 'Price' parsing
    public function price($string)
    {
    }

    // *** 'is-offer' filtering
    /**
     * List of 'offering' words.
     */
    const OFFERS = [
        'ponukam', 'ponukame',
        'ponúkam', 'ponúkame',
    ];

    public function isOffer($string)
    {
        return $this->containsOneOf($string, self::OFFERS);
    }

    // *** 'free-seats' parsing
    public function freeSeats($post)
    {
        // TODO: Implement freeSeats() method.
    }

    // *** 'is-request' filtering
    /**
     * List of 'requests' words.
     */
    const REQUESTS = [
        'hladam', 'hladame',
        'hladam', 'hladame',
        'hľadam', 'hľadame',
        'hľadám', 'hľadáme',
    ];

    public function isRequest($string)
    {
        return $this->containsOneOf($string, self::REQUESTS);
    }

    // *** Helper functions
    private function containsOneOf($string, array $words)
    {
        $strings = array_map('strtolower', explode(' ', $string));
        $intersect = array_intersect($strings, $words);

        if (count($intersect) != 1) {
            return null;
        }

        return array_pop($intersect);
    }
}

