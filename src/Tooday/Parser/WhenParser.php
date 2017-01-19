<?php

namespace Tooday\Parser;

use Tooday\Utils;
use Tooday\Exceptions\ParserException;

/**
 * Implementation of Slovak when-parser.
 *
 * @package Tooday\Parser
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
class WhenParser implements Parser
{
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
        'nedelu'   => 0,
        'pondelok' => 1,
        'utorok'   => 2,
        'stredu'   => 3,
        'streda'   => 3,
        'stvrtok'  => 4,
        'štvrtok'  => 4,
        'piatok'   => 5,
        'sobotu'   => 6,
        'sobota'   => 6,
    ];

    /**
     * Regular expressions for parsing times.
     *  Ex. ... 13:21 ...
     *      ... 8. hod ...
     */
    const WHEN_REGEX_TIME_REGULAR = '/\d{1,2}\s*:\s*\d{1,2}/';
    const WHEN_REGEX_TIME_HOUR = '/(\d{1,2})\.*\s*(?:hod|h|hodine|hodinou){1}/';

    public function parse($post)
    {
        $when = [];

        $date = $this->getDate($post);
        if ($date) {
            $when['date'] = $date;
        }

        $time = $this->getTime($post);
        if ($time) {
            $when['time'] = $time;
        }

        return $when;
    }

    private function getDate($post)
    {
        $fromDay = "";
        $fromAdverb = "";

        $day = Utils::containsOneOf($post, array_keys(self::DAYS));
        $adverb = Utils::containsOneOf($post, self::ADVERBS);
        if ($adverb) {
            $fromAdverb = $this->getDateFromAdverb($adverb);
        }
        if ($day) {
            $fromDay = $this->getDateFromDay($day);
        }

        if ($fromAdverb && $fromDay && $fromAdverb !== $fromDay) {
            throw new ParserException('Dates are not consistent');
        }

        return $fromAdverb ?: $fromDay;

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
        if ($add < 0) { // Next week
            $add += 7;
        }

        return date('Y-m-d', strtotime("+ $add days"));
    }

    private function getTime($post)
    {
        $match = [];

        if (preg_match(self::WHEN_REGEX_TIME_REGULAR, $post, $match)) {
            return $match[0];
        } else if (preg_match(self::WHEN_REGEX_TIME_HOUR, $post, $match)) {
            $hour = $match[1];

            return "$hour:00";
        }

        return $match;
    }
}
