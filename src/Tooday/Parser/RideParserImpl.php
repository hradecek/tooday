<?php

namespace Tooday\Parser;

use Exception;

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
     * Matching vars:
     *   - <from>
     *   - <to>
     */
    const WHERE_REGEX_ARROWS = '/(?<from>\w+)\s*((->|-|=>|~>|-->|>|>>){1}\s*(?<to>\w+))/';

    /**
     * Regular expression for place parsing.
     * Parsing 'from-to' style:
     *   Ex. ... from City1 to City2 ...
     */
    const WHERE_REGEX_FROM_TO = '/z\s+(?<from>\w+)\s+do\s+(?<to>\w+)/';

    public function where($post)
    {
        $where = [];
        
        preg_match(self::WHERE_REGEX_ARROWS, $post, $where) || 
        preg_match(self::WHERE_REGEX_FROM_TO, $post, $where);

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
        'nedela'   => 7,
        'nedeľa'   => 7,
    ];

    public function when($string)
    {
        $when = array();
        
        $adverb = $this->containsOneOf($string, self::ADVERBS);
        $day = $this->containsOneOf($string, self::DAYS);
        if ($adverb) {
            $when['date'] = $this->getDateFromAdverb($adverb);
        } else if ($day) {
            $when['date'] = $day;
        }
        return $when;
    }
    
    private function getCurrentDayInWeek()
    {
        $date = date('Y-m-d');
        $day = date('N', strtotime($date));
        
        return $day;
    }

    private function getDateFromAdverb($adverb)
    {
        switch ($adverb) {
            case 'dnes':
                $add = 0;
                break;
            case 'zajtra':
                $add = 1;
                break;
            case 'pozajtra':
                $add = 2;
                break;
            case 'popozajtra':
                $add = 2;
                break;
            default:
                throw new Exception('there is not such an adverb');
        }
        $current = date('Y-m-d');

        return date('Y-m-d', strtotime($Date . " + $add days"));
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
