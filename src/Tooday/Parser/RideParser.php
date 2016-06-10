<?php

/**
 * @author Ivo Hradek <ivohradek@gmail.com>
 */
interface RideParser
{
    /**
     *
     */
    public function price($string);

    /**
     *
     */
    public function when($string);

    /**
     *
     */
    public function where($string);

    /**
     *
     */
    public function freeSeats($string);
}

