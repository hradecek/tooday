<?php

namespace Tooday\Parser;

/**
 * <p>This is facade for Slovak ride-parser.</p>
 *
 * @package Tooday\Parser
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
class RideParser implements RideParserFacade
{
    /**
     * @var WhereParser - parsing places (from, to, ...)
     */
    private $whereParser;

    /**
     * @var WhenParser - parsing times and dates
     */
    private $whenParser;
    
    public function __construct()
    {
        $this->whenParser = new WhenParser;
        $this->whereParser = new WhereParser;
    }
    
    public function where($post)
    {
        return $this->whereParser->parse($post);
    }
    
    public function when($post)
    {
        return $this->whenParser->parse($post);
    }
    
    // *** 'Price' parsing
    public function price($string)
    { }

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
        // return $this->containsOneOf($string, self::OFFERS);
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
        // return $this->containsOneOf($string, self::REQUESTS);
    }
}

