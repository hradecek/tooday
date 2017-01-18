<?php

namespace Tooday\Parser;

/**
 * <p>
 * Simple parser for a so called "ride post"
 * </p>
 * 
 * @package Tooday\Parser
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
interface RideParser
{
    /**
     * <p>
     * Get offered <b>price</b>
     * If such an information is not available return null.
     * </p>
     * 
     * @param string $post
     * @return double|null
     */
    public function price($post);

    /**
     * <p>Get <b>time</b> info about the ride.</p>
     *
     * <p>
     * Returned array format:
     *   array['date'] - date in the format dd/mm/YYYY
     *   array['time'] - time in the format HH:MM
     * </p>
     *
     * @param string $post
     * @return array
     */
    public function when($post);

    /**
     * <p>Get <b>place</b> info about the ride.</p>
     * 
     * <p>
     * Returned array format:
     *   array['from'] - place 'from' ride is offered; start point
     *   array['to'] - place 'to' ride is offered; end point
     *   array['through'] - array (optional); in case ride offering 'through' points
     * </p>
     * 
     * @param string $post
     * @return array
     */
    public function where($post);

    /**
     * <p>
     * Get information about <b>free seats</b> which are offered.
     * If there are no such information null is returned.
     * </p>
     * 
     * @param string $post
     * @return integer|null - number of free seats if available, otherwise null
     */
    public function freeSeats($post);
}

