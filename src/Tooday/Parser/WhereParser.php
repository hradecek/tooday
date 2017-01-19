<?php

namespace Tooday\Parser;

/**
 * Implementation of Slovak where-parser.
 *
 * @package Tooday\Parser
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
class WhereParser implements Parser
{
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

    public function parse($post)
    {
        $match = [];

        if (preg_match(self::WHERE_REGEX_ARROWS, $post, $match)) {
            return $this->whereArrow($match[0]);
        } else if (preg_match(self::WHERE_REGEX_FROM_TO, $post, $match)) {
            $where['from'] = $match['from'];
            $where['to'] = $match['to'];
            return $where;
        }

        return $match;
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
}
